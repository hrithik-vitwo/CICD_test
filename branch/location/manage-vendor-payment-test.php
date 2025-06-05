<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");


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

$grnObj = new GrnController();
$BranchSoObj = new BranchSo();
$fetchInvoiceByCustomer = $grnObj->fetchGRNInvoice()['data'];


if (isset($_POST['addNewSOFormSubmitBtn'])) {
    // console($_POST);
    // exit;
    $addBranchSo = $BranchSoObj->addBranchSo($_POST);
    // console($addBranchSo);
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

?>

<style>
    /* .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }

  .display-flex-gap {
    gap: 0 !important;
  }

  .card-body.others-info.vendor-info.so-card-body {
    height: 250px !important;
  }

  .fob-section div {
    align-items: center;
    gap: 3px;
  }

  .so-delivery-create-btn {
    display: flex;
    align-items: center;
    gap: 20px;
    max-width: 250px;
    margin-left: auto;
  }

  .customer-modal .modal-header {
    height: 250px !important;
  }


  .display-flex-space-between p {
    width: 77%;
    text-align: left;
  }

  @media (max-width: 575px) {

    .filter-serach-row {
      align-items: center;
      padding-top: 9px;
      margin-bottom: 0 !important;
    }

    .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
      padding: 7px;
    }

    .card-body.others-info.vendor-info.so-card-body {
      height: auto !important;
    }

    .customer-modal .modal-header {
      height: 285px !important;
    }

    .customer-modal .nav.nav-tabs {
      top: 0 !important;
    }

  } */


    .content-wrapper table tr:nth-child(2n+1) td {
        background: #b5c5d3;
    }

    tfoot.individual-search tr th {
        padding: 5px !important;
        border-right: 1px solid #fff !important;
    }

    .vertical-align {
        vertical-align: middle;
    }

    /* .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  } */

    .dataTables_scrollHeadInner tr th {
        position: sticky;
        top: -1px;
    }

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row {
        display: flex !important;
        align-items: center;
        justify-content: end;
    }

    /* div.dataTables_wrapper {
    overflow: hidden;
  } */

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(1),
    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(3) {
        padding: 10px 20px;
    }

    div.dataTables_wrapper div.dataTables_length select {
        width: 60% !important;
        appearance: none !important;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .dataTables_scroll {
        position: relative;
        margin-bottom: 10px;
    }

    .dataTables_scroll::-webkit-scrollbar {
        visibility: hidden;
    }

    .dataTables_scrollBody tfoot th {
        background: none !important;
    }

    .dataTables_scrollHead {
        margin-bottom: 40px;
    }

    .dataTables_scrollBody {
        max-height: 75vh !important;
        height: 75% !important;
        overflow: scroll !important;
    }

    .dataTables_scrollFoot {
        position: absolute;
        top: 37px;
        height: 50px;
        overflow: scroll;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 10px;
    }

    div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
        border: 0;
    }

    .dataTables_filter {
        padding-right: 0 !important;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        padding: 0;
        border: 0;
    }

    .dt-top-container {
        display: flex;
        align-items: center;
        padding: 0 20px;
        gap: 20px;
    }

    .transactional-book-table tr td {
        white-space: pre-line !important;
    }

    .dataTables_length {
        margin-left: 4em;
    }

    a.btn.add-col.setting-menu.waves-effect.waves-light {
        position: absolute !important;
        display: flex;
        justify-content: space-between;
        top: 10px !important;
    }

    div.dataTables_wrapper div.dataTables_length label {
        margin-bottom: 0;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-left: 20px;
        position: relative;
        top: 0;
    }

    .dataTables_paginate {
        position: relative;
        right: 20px;
        bottom: 20px;
        margin-top: -15px;
    }

    .dt-center-in-div {
        display: block;
        /* order: 3; */
        margin-left: auto;
    }

    .dt-buttons.btn-group.flex-wrap button {
        background-color: #003060 !important;
        border-color: #003060 !important;
        border-radius: 7px !important;
    }

    /* .setting-row .col .btn.setting-menu {
    position: absolute !important;
    right: 255px;
    top: 10px;
  } */

    .dt-buttons.btn-group.flex-wrap {
        gap: 10px;
    }


    table.dataTable>thead .sorting:before,
    table.dataTable>thead .sorting:after,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_asc:after,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_desc:after,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_asc_disabled:after,
    table.dataTable>thead .sorting_desc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled:after {

        display: block !important;

    }

    .dataTable thead tr th,
    .dataTable tfoot.individual-search tr th {
        padding-right: 30px !important;
        border-right: 0 !important;
    }

    select.fy-dropdown {
        position: absolute;
        max-width: 100px;
        top: 14px;
        left: 255px;
    }

    .daybook-filter-list.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-end;
        position: relative;
        top: -35px;
        left: -75px;
        float: right;
    }

    .daybook-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .vendor-invoice-tab.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-start;
        position: relative;
        top: 0;
        left: 0;
    }

    .vendor-invoice-tab.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }



    @media (max-width: 769px) {
        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: absolute;
            top: -39px;
            right: 60px;
        }

        .dt-buttons.btn-group.flex-wrap button {
            max-width: 60px;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: -10px;
        }


    }

    @media (max-width :575px) {
        .dataTables_scrollFoot {
            position: absolute;
            top: 28px;
        }

        .dt-top-container {
            display: flex;
            align-items: baseline;
            padding: 0 20px;
            gap: 20px;
            flex-direction: column-reverse;
            flex-wrap: nowrap;
        }

        .dataTables_length {
            margin-left: 0;
            margin-bottom: 1em;
        }

        select.fy-dropdown {
            position: absolute;
            max-width: 125px;
            top: 155px;
            left: 189px;
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 164px !important;
        }

        .dt-center-in-div {
            margin: 3px auto;
        }

        div.dataTables_filter {
            right: 0;
            margin-top: 0;
            position: relative;
            right: -43px;
        }

        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: relative;
            top: 0;
            right: 0;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: 40px;
        }

        .dataTables_length label {
            font-size: 0;
        }
    }

    @media (max-width: 376px) {
        div.dataTables_wrapper div.dataTables_filter {
            margin-top: 0;
            padding-left: 0 !important;
        }

        select.fy-dropdown {
            position: absolute;
            max-width: 109px;
            top: 144px;
            left: 189px;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            max-width: 150px;
        }

        select.fy-dropdown {
            max-width: 100px;
        }

        /* div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    } */
    }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<!-- <link rel="stylesheet" href="../../public/assets/accordion.css"> -->
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="card card-tabs">
                        <div class="p-0 pt-1 my-2">
                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                    <h3 class="card-title">Manage Vendor Payment</h3>
                                    <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                                </li>
                            </ul>
                        </div>
                        <div class="card card-tabs mb-0" style="border-radius: 20px;">
                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                            <a type="button" class="btn btn-info" data-toggle="modal" id="initiate_id" style="position:absolute;"> Proceed Payment </a>
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
                                                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Vendor Invoice</h5>
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


                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #dbe5ee; overflow: auto;">

                                    <?php
                                    $cond = '';

                                    // $sts = " AND grniv.`grnStatus`!='deleted'";
                                    // if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                    //     $sts = ' AND grniv.`grnStatus`="' . $_REQUEST['status_s'] . '"';
                                    // }

                                    // if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    //     $cond .= " AND grniv.`postingDate` between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    // }


                                    // if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                    //     $cond .= " AND (grniv.`vendorDocumentNo` like '%" . $_REQUEST['keyword2'] . "%' OR grniv.`grnCode` like '%" . $_REQUEST['keyword2'] . "%' OR grniv.`grnIvCode` like '%" . $_REQUEST['keyword2'] . "%')";
                                    // } else {
                                    //     if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                    //         $cond .= " AND (grniv.`vendorDocumentNo` like '%" . $_REQUEST['keyword'] . "%'  OR grniv.`grnCode` like '%" . $_REQUEST['keyword'] . "%' OR grniv.`grnIvCode` like '%" . $_REQUEST['keyword'] . "%')";
                                    //     }
                                    // }

                                    $sql_list =  "SELECT
                                    MAX(invoice_id) AS invoice_id,
                                    CODE,
                                    erp_payment_initiate_request.vendor_id,
                                    MAX(vendor_code) AS vendor_code,
                                    MAX(trade_name) AS vendor_name,
                                    MAX(vendor_gstin) AS gst,
                                    MAX(erp_vendor_bank_details.vendor_bank_account_no) AS vendor_bank_account_no,
                                    MAX(erp_vendor_bank_details.vendor_bank_name) AS vendor_bank_name,
                                    MAX(erp_payment_initiate_request.created_at) AS max_created_at
                                FROM
                                    `erp_payment_initiate_request`
                                    LEFT JOIN `erp_vendor_details` ON erp_vendor_details.`vendor_id` = erp_payment_initiate_request.`vendor_id` LEFT JOIN `erp_vendor_bank_details` ON erp_vendor_bank_details.`vendor_id` = erp_vendor_details.`vendor_id`
                                WHERE
                                    erp_payment_initiate_request.`branch_id` = '$branch_id' AND erp_payment_initiate_request.`location_id` = '$location_id' AND erp_payment_initiate_request.`company_id` = '$company_id'
                                GROUP BY
                                    `code`,
                                    `vendor_id`,erp_payment_initiate_request.`created_at` ORDER BY
                                    erp_payment_initiate_request.`created_at`
                                DESC";

                                    // $sql_list = "SELECT grniv.*, grn.`grnCreatedAt` AS grnDate, grn.`po_date` AS poDate FROM `" . ERP_GRNINVOICE . "` AS grniv, `erp_grn` AS grn WHERE 1 " . $cond . " AND grniv.`companyId`='$company_id' AND grniv.`grnId` = grn.`grnId` AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' AND grniv.`paymentStatus`='15' " . $sts . " ORDER BY grniv.`grnIvId` DESC limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                    $qry_list = queryGet($sql_list, true);
                                    $num_list = $qry_list['numRows'];

                                    // console($qry_list);

                                   $countShow = "SELECT
                                        count(*)
                                    FROM
                                        `erp_payment_initiate_request`
                                        LEFT JOIN `erp_vendor_details` ON erp_vendor_details.`vendor_id` = erp_payment_initiate_request.`vendor_id` LEFT JOIN `erp_vendor_bank_details` ON erp_vendor_bank_details.`vendor_id` = erp_vendor_details.`vendor_id`
                                    WHERE
                                        erp_payment_initiate_request.`branch_id` = '$branch_id' AND erp_payment_initiate_request.`location_id` = '$location_id' AND erp_payment_initiate_request.`company_id` = '$company_id'
                                    GROUP BY
                                        erp_payment_initiate_request.`code`,
                                        erp_payment_initiate_request.`vendor_id`";

                                        // console($qry_list);

                                    // $countShow = "SELECT count(*) FROM `" . ERP_GRNINVOICE . "` AS grniv, `erp_grn` AS grn WHERE grniv.`companyId`='$company_id' AND grniv.`grnId` = grn.`grnId` AND grniv.`branchId`='$branch_id' AND grniv.`locationId`='$location_id' AND grniv.`paymentStatus`='15' " . $sts . " ORDER BY grniv.`grnIvId` DESC";
                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    $count = $rowCount[0];

                                    console($qry_list["data"]);

                                    ?>

                                    <table id="dataTable" class="table table-hover transactional-book-table" data-paging="true" data-responsive="false" style="position: relative;">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Request Code</th>
                                                <th>Vendor Code</th>
                                                <th>Vendor Name</th>
                                                <th>Vendor GSTIN</th>
                                                <th>Bank Name</th>
                                                <th>Bank Account Number</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sl = 0;
                                            foreach ($qry_list["data"] as $key => $one) {
                                            ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" id="check_box_<?= $key ?>" name="check_box" class="checkbx" value="<?= $one['vendor_id'] ?>">
                                                    </td>
                                                    <td><?= $one['CODE'] ?></td>
                                                    <td><?= $one['vendor_code'] ?></td>
                                                    <td><?= $one['vendor_name'] ?></td>
                                                    <td><?= $one['gst'] ?></td>
                                                    <td><?= $one['vendor_bank_name'] ?></td>
                                                    <td><?= $one['vendor_bank_account_no'] ?></td>
                                                    <td></td>
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
                                </div>
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

    $(document).ready(function() {
        $("#initiate_id").click(function(e) {
            if ($("input:checkbox[class=checkbx]:checked").length === 0) {
                alert("Select Atleast one check-box");
            } else {
                var yourArray = [];
                $("input:checkbox[class=checkbx]:checked").each(function() {
                    yourArray.push($(this).val());
                });
                window.location.href = `<?= LOCATION_URL ?>vendor-multipayments-fresh.php?code=${btoa(JSON.stringify(yourArray))}`;
            }
        });
    });
</script>
<?php
require_once("../common/footer.php");
?>