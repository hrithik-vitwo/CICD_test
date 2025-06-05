<?php
require_once("../../app/v1/connection-branch-admin.php");

//administratorLocationAuth();

require_once("../common/header.php");

require_once("../common/navbar.php");

require_once("../common/sidebar.php");

require_once("../common/pagination.php");

require_once("../../app/v1/functions/branch/func-goods-controller.php");

require_once("../../app/v1/functions/branch/func-bom-controller.php");

require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");



$goodsController = new GoodsController();

$goodsBomController = new GoodsBomController();





if (isset($_POST["creategoodsdata"])) {

    //console($_POST);
    $addNewObj = $goodsController->createGoods($_POST);


    if ($addNewObj["status"] == "success") {
        // console($_POST);
        // exit();
        swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"], BASE_URL . "branch/location/goods.php");
    } else {
        swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"]);
    }

    //swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
}

if (isset($_POST["createLocationItem"])) {
    $addNewObj = $goodsController->createGoodsLocation($_POST);



    swalToast($addNewObj["status"], $addNewObj["message"]);
}




if (isset($_POST["editgoodsdata"])) {

    $addNewObj = $goodsController->editGoods($_POST);

    swalToast($addNewObj["status"], $addNewObj["message"]);
}



if (isset($_POST["add-table-settings"])) {

    $editDataObj = updateInsertTableSettings($_POST,  $_SESSION["logedBranchAdminInfo"]["adminId"]);

    swalToast($editDataObj["status"], $editDataObj["message"]);
}

?>



<link rel="stylesheet" href="../../public/assets/listing.css">

<link rel="stylesheet" href="../../public/assets/sales-order.css">

<link rel="stylesheet" href="../../public/assets/accordion.css">


<style>
    .item_desc {

        display: block;

        width: 100%;

        padding: 0.375rem 0.75rem;

        font-size: 11px;

        font-weight: 400;

        line-height: 1.5;

        color: #212529;

        border: 1px solid rgb(201 201 201);

        background-color: #fff;

        background-clip: padding-box;

        appearance: none;

        border-radius: 0.25rem;

        transition: box-shadow .15s ease-in-out;

    }

    .label-hidden {
        visibility: hidden;
    }

    .calculate-hsn-row {
        align-items: baseline;
        padding-right: 0;
    }

    .btn-transparent {
        position: absolute;
        top: 23px;
        left: 9px;
        height: 35px;
        z-index: 9;
        width: 92%;
        background: transparent !important;
    }

    .hsn-dropdown-modal .modal-dialog {

        max-width: 700px;

    }

    .hsn-dropdown-modal .modal-dialog .modal-header h4 {

        font-size: 15px;
        margin-bottom: 0;
        white-space: nowrap;

    }

    .hsn-dropdown-modal .modal-dialog .modal-header input {

        max-width: 300px;
        font-size: 12px;
        height: 30px;
        margin: 0;
        margin: 0;
        border: 1px solid #c3c3c3;
        box-shadow: none;
    }

    input.serachfilter-hsn {
        width: 40% !important;
    }

    .hsn-dropdown-modal .modal-body {
        overflow: hidden;
    }

    .hsn-dropdown-modal .modal-body .card {

        background: none;

    }

    .hsn-dropdown-modal .modal-body .card .card-body {

        background: #dbe5ee;

        box-shadow: 3px 5px 11px -1px #0000004d;

    }

    .hsn-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .hsn-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
    }

    .hsn-title h5 {
        margin-bottom: 0;
        font-size: 15px;
        font-weight: 600;
    }

    .tax-per p {
        font-size: 11px;
        font-style: italic;
        font-weight: 600;
        color: #343434;
    }

    .hsn-description p {
        font-size: 12px;
    }

    .highlight {
        background-color: yellow
    }

    .select2-container {
        width: 100% !important;
    }

    .hsn-modal-table tbody td {
        white-space: pre-line !important;
    }

    .hsn-modal-table tbody tr:nth-child(even) td {
        background-color: #b4c7d9;
    }

    .card-body.hsn-code div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(3) {
        display: flex;
        position: relative;
        top: 0;
        right: 0;
        justify-content: end;
        padding: 15px;
    }

    .card-body.hsn-code div.dataTables_wrapper div.dataTables_info {
        display: none;
    }


    .card-body.hsn-code div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0;
        display: inline-block;
        width: auto;
        padding-left: 30px;
        border: 1px solid #8f8f8f;
        color: #1B2559;
        height: 30px;
        border-radius: 8px;
        margin-left: 10px;
    }

    .row.calculate-row {
        justify-content: end;
    }

    .hsn-column {
        padding-right: 0;
    }


    .hsn-dropdown-modal .modal-body {

        max-height: 100%;

        height: 500px;

    }


    .hsn-dropdown-modal .icons-container {
        position: absolute;
        top: 18px;
        right: 0;
        bottom: 0;
        width: 70px;
        height: 30px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icons-container i {
        color: #9b9b9b;
        font-size: 14px;
    }

    .icon-close {
        position: absolute;
        display: flex;
        align-items: center;
        gap: 5px;
        right: 30px;
    }

    .modal-content.card {
        box-shadow: 1px 1px 19px #4f4f4f;
    }

    p.hsn-description-info {
        /* display: none; */
        max-height: 60px;
        font-size: 10px !important;
        overflow: auto;
    }

    .unit-measure-col,
    .hsn-modal-col {
        border: 1px dashed #8192a3;
        padding-bottom: 11px;
        border-radius: 12px;
        width: 49%;
    }

    .row.basic-info-form-view {
        justify-content: center;
    }

    .dash-border-row {
        justify-content: space-between;
    }


    .serach-input-section button {
        position: absolute;
        border: none;
        display: block;
        width: 15px;
        height: 15px;
        line-height: 16px;
        font-size: 12px;
        border-radius: 50%;
        top: -47em;
        bottom: 0;
        right: 27px;
        margin: auto;
        background: #ddd;
        padding: 0;
        outline: none;
        cursor: pointer;
        transition: .1s;
    }

    .head-title p.heading.lead {
        font-size: 14px;
        font-weight: 300;
    }

    .head-title p {
        white-space: pre-wrap;
        margin: 15px 0;
        line-height: 25px;
    }

    .head-title .item-desc {
        line-height: 18px;
        font-size: 11px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .modal.goods-type-modal .modal-header {
        height: 380px;
    }

    .item-specification-row .item-img {
        margin-left: 0;
        width: 200px;
        height: 100%;
        position: relative;
        top: 0;
    }

    .item-specification-row .item-img img {
        max-width: 100%;
    }

    .detail-view-accordion .display-flex-space-between p:nth-child(2) {
        position: absolute;
        left: 19%;
        text-align: left;
    }

    .detail-view-accordion .display-flex-space-between p.group-desc {
        width: 170px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .trailLi a:last-child {
        display: none;
    }

    .group-max-width {
        width: 350px;
    }

    @media(max-width: 575px) {
        .hsn-column {
            padding-left: 0;
            padding-right: 15px;
        }

        .base-measure {
            padding-right: 15px !important;
        }

        .calculate-row .col {
            width: 20%;
            padding: 0;
        }

        .calculate-row .col input {
            width: 20px !important;
        }

        .calculate-parent-row .col:nth-child(1) {
            padding-left: 15px;
        }

        .calculate-row {
            padding: 0 15px;
            justify-content: center !important;
        }
    }
</style>


<?php
if (isset($_GET['sfg'])) {
?>
    <div class="content-wrapper is-goods is-goods-type-item is-goods-sfg">

        <!-- Content Header (Page header) -->



        <!-- Main content -->

        <section class="content">

            <div class="container-fluid">





                <!-- row -->

                <div class="row p-0 m-0">

                    <div class="col-12 mt-2 p-0">



                        <!-- <ol class="breadcrumb bg-transparent">

  <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

  <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Items</a></li>


</ol> -->

                        <div class="p-0 pt-1 my-2">

                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                    <h3 class="card-title">
                                        Item Master
                                    </h3>


                                    <a href="goods.php?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>

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

                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

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
                                                            <a href="goods.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                                                            <a href="goods-type-items.php" class="btn "><i class="fa fa-list mr-2 "></i>Raw Materials</a>
                                                            <a href="goods-type-items.php?sfg" class="btn active"><i class="fa fa-clock mr-2 active"></i>SFG</a>
                                                            <a href="goods-type-items.php?fg" class="btn "><i class="fa fa-lock-open mr-2 "></i>FG</a>
                                                            <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                                                            <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                                                        </div>
                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="goods.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                                <a href="goods-type-items.php" class="btn"><i class="fa fa-list mr-2"></i>Raw Materials</a>
                                                                <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                                                                <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                                                                <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                                                                <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                                                            </div>
                                                        </div>

                                                        <div class="section serach-input-section">

                                                            <input type="text" id="myInput" name="keyword" placeholder="" class="field form-control" value="<?php echo $keywd; ?>" />

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

                                                <div class="col-lg-1 col-md-1 col-sm-1">

                                                    <a href="goods.php?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

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

                            <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                                        <?php

                                        $cond = '';
                                        $sts = " AND `status` !='deleted'";
                                        if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                            $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                        }
                                        if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                            $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                        }
                                        if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                            $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR `itemName` like '%" . $_REQUEST['keyword2'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword2'] . "%')";
                                        } else {

                                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                                                $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                                            }
                                        }




                                        $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . " AND  `goodsType`=2 AND `company_id`=$company_id  ORDER BY itemId desc  ";

                                        $qry_list = mysqli_query($dbCon, $sql_list);

                                        $num_list = mysqli_num_rows($qry_list);





                                        $countShow = "SELECT count(*) FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id AND `goodsType`=2 ";

                                        $countQry = mysqli_query($dbCon, $countShow);

                                        $rowCount = mysqli_fetch_array($countQry);

                                        $count = $rowCount[0];

                                        $cnt = $GLOBALS['start'] + 1;

                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                                        $settingsCheckbox = unserialize($settingsCh);

                                        if ($num_list > 0) { ?>

                                            <table class="table defaultDataTable table-hover text-nowrap">

                                                <thead>

                                                    <tr class="alert-light">

                                                        <!-- <th>#</th> -->

                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                            <th>Item Code</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Item Name</th>

                                                        <?php }

                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Base UOM</th>

                                                        <?php  }

                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Group</th>

                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>

                                                            <th>Type</th>

                                                        <?php

                                                        }

                                                        if (in_array(6, $settingsCheckbox)) { ?>

                                                            <th>Moving Weighted Price</th>

                                                        <?php  }

                                                        if (in_array(7, $settingsCheckbox)) { ?>

                                                            <th>Valuation Class</th>

                                                        <?php

                                                        }

                                                        if (in_array(8, $settingsCheckbox)) { ?>

                                                            <th> Target Price </th>

                                                        <?php

                                                        }




                                                        ?>

                                                        <th>BOM Status</th>

                                                        <th>Status</th>

                                                        <th>Action</th>
                                                        <th>Add</th>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <?php

                                                    $customerModalHtml = "";

                                                    while ($row = mysqli_fetch_assoc($qry_list)) {
                                                        //console($row);
                                                        $itemId = $row['itemId'];
                                                        $itemCode = $row['itemCode'];

                                                        $itemName = $row['itemName'];

                                                        $netWeight = $row['netWeight'];

                                                        $volume = $row['volume'];

                                                        $goodsType = $row['goodsType'];

                                                        $grossWeight = $row['grossWeight'];

                                                        $buom_id = $row['baseUnitMeasure'];

                                                        $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                                                        $buom = $buom_sql['data']['uomName'];
                                                        //  console($buom);



                                                        $goodTypeId = $row['goodsType'];
                                                        $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                                                        $type_name = $type_sql['data']['goodTypeName'];



                                                        $goodGroupId = $row['goodsGroup'];
                                                        $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                                                        $group_name = $group_sql['data']['goodGroupName'];


                                                        $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                                                        $mwp = $summary_sql['data']['movingWeightedPrice'];
                                                        $val_class = $summary_sql['data']['priceType'];

                                                    ?>

                                                        <tr>

                                                            <!-- <td><?= $cnt++ ?></td> -->

                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                                <td><?= $row['itemCode'] ?></td>

                                                            <?php }

                                                            if (in_array(2, $settingsCheckbox)) { ?>

                                                                <td>
                                                                    <p class="pre-normal"><?= $row['itemName'] ?></p>
                                                                </td>

                                                            <?php }

                                                            if (in_array(3, $settingsCheckbox)) { ?>

                                                                <td><?= $buom ?> </td>

                                                            <?php }

                                                            if (in_array(4, $settingsCheckbox)) { ?>

                                                                <td>
                                                                    <p class="pre-normal group-max-width"><?= $group_name ?></p>
                                                                </td>

                                                            <?php }
                                                            if (in_array(5, $settingsCheckbox)) { ?>

                                                                <td><?= $type_name ?></td>

                                                            <?php }
                                                            if (in_array(6, $settingsCheckbox)) { ?>

                                                                <td><?= $mwp ?></td>

                                                            <?php }

                                                            if (in_array(7, $settingsCheckbox)) { ?>

                                                                <td><?= $val_class ?></td>

                                                            <?php }

                                                            if (in_array(8, $settingsCheckbox)) { ?>

                                                                <td></td>

                                                            <?php }

                                                            ?>



                                                            <td>

                                                                <?php

                                                                if ($row['isBomRequired'] == 1) {



                                                                    echo '<span class="status">Required</span>';
                                                                } else {

                                                                    echo '<span class="status-danger">Not Required</span>';
                                                                }


                                                                ?>

                                                            </td>



                                                            <td>

                                                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                                                                    <input type="hidden" name="id" value="<?php echo $row['itemId'] ?>">

                                                                    <input type="hidden" name="changeStatus" value="active_inactive">

                                                                    <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">

                                                                        <?php if ($row['status'] == "active") { ?>

                                                                            <span class="status"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } else if ($row['status'] == "inactive") { ?>

                                                                            <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } else if ($row['status'] == "draft") { ?>

                                                                            <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } ?>



                                                                    </button>

                                                                </form>

                                                            </td>

                                                            <td>



                                                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" class="btn btn-sm">

                                                                    <i class="fa fa-eye po-list-icon"></i>

                                                                </a>

                                                            </td>

                                                            <td>
                                                                <?php
                                                                $item_id = $row['itemId'];
                                                                $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE  `location_id`=$location_id  AND `itemId`=$item_id ", true);
                                                                if ($check_sql['status'] == "success") {

                                                                ?>
                                                                    <button class="btn btn-success" type="button">Added</button>

                                                                <?php

                                                                } else {

                                                                ?>


                                                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $row['itemId'] ?>">Add</button>
                                                                <?php
                                                                }

                                                                ?>
                                                            </td>

                                                        </tr>


                                                        <!-----add form modal start --->
                                                        <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <form method="POST" action="">
                                                                            <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                                                                            <input type="hidden" name="item_id" value="<?= $row['itemId'] ?>">

                                                                            <div class="row">

                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                        <div class="card-header">

                                                                                            <h4>Storage Details</h4>

                                                                                        </div>

                                                                                        <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                    <div class="row goods-info-form-view customer-info-form-view">









                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Storage Control</label>

                                                                                                                <input type="text" name="storageControl" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Max Storage Period</label>

                                                                                                                <input type="text" name="maxStoragePeriod" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                            <div class="form-input">
                                                                                                                <label class="label-hidden" for="">Min Time Unit</label>
                                                                                                                <select id="minTime" name="minTime" class="select2 form-control">
                                                                                                                    <option value="">Min Time Unit</option>
                                                                                                                    <option value="Day">Day</option>
                                                                                                                    <option value="Month">Month</option>
                                                                                                                    <option value="Hours">Hours</option>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Minimum Remain Self life</label>

                                                                                                                <input type="text" name="minRemainSelfLife" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                            <div class="form-input">
                                                                                                                <label class="label-hidden" for="">Max Time Unit</label>
                                                                                                                <select id="maxTime" name="maxTime" class="select2 form-control">
                                                                                                                    <option value="">Max Time Unit</option>
                                                                                                                    <option value="Day">Day</option>
                                                                                                                    <option value="Month">Month</option>
                                                                                                                    <option value="Hours">Hours</option>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                </div>




                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                        <div class="card-header">

                                                                                            <h4>Pricing and Discount

                                                                                                <span class="text-danger">*</span>

                                                                                            </h4>

                                                                                        </div>

                                                                                        <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                    <div class="row goods-info-form-view customer-info-form-view">

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Target price</label>

                                                                                                                <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Max Discount</label>

                                                                                                                <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                                                                                    </div>


                                                                                </div>





                                                                            </div>










                                                                        </form>

                                                                    </div>
                                                                    <div class="modal-body" style="height: 500px; overflow: auto;">
                                                                        <div class="card">

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!---end modal --->


                                                        <!-- right modal start here  -->

                                                        <div class="modal fade right goods-modal goods-type-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                <!--Content-->

                                                                <div class="modal-content">

                                                                    <!--Header-->

                                                                    <div class="modal-header pt-4">

                                                                        <div class="row item-specification-row">

                                                                            <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                <div class="item-img">

                                                                                    <img src="../../public/assets/img/image/goods-item-image.png" title="goods-iem-image" alt="goods_item_image">

                                                                                </div>

                                                                            </div>

                                                                            <div class="col-lg-8 col-md-8 col-sm-8">

                                                                                <div class="head-title">

                                                                                    <p class="heading lead">Item Name : <?= $itemName ?></p>

                                                                                    <p class="item-code">Item Code : <?= $itemCode ?></p>

                                                                                    <p class="item-desc">Description : <?= $row['itemDesc'] ?></p>

                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <div class="display-flex-space-between mt-4 mb-3 location-master-action">
                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="home<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="true">Info</a>
                                                                                </li>

                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" data-ccode="<?= $row['ItemCode'] ?>" href="#history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="false"><i class="fas fa-history mr-2" aria-hidden="true"></i>Trail</a>
                                                                                </li>
                                                                                <!---------------------Audit History Button End--------------------------->
                                                                            </ul>



                                                                            <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                <?php $itemId = base64_encode($row['itemId']) ?>

                                                                                <form action="" method="POST">



                                                                                    <!-- <a href="goods.php?edit=<?= $itemId ?>" name="customerEditBtn">

                            <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                          </a> -->

                                                                                    <a href="">

                                                                                        <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                    </a>

                                                                                    <a href="">

                                                                                        <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                    </a>

                                                                                </form>

                                                                            </div>

                                                                        </div>

                                                                    </div>



                                                                    <!--Body-->

                                                                    <div class="modal-body" style="padding: 0;">

                                                                        <!-- <ul class="nav nav-tabs" style="padding-left: 16px;" id="myTab" role="tablist">

                        <li class="nav-item">

                          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Info</a>

                        </li>

                        <li class="nav-item">

                          <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">BOM</a>

                        </li>



                      </ul> -->





                                                                        <div class="tab-content" id="myTabContent">



                                                                            <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                                <div class="row">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                        <?php if ($row['bomStatus'] != 0) { ?>

                                                                                            <a href="goods.php?bom=<?= $itemId; ?>" class="btn btn-primary float-right m-3" name="customerEditBtn">

                                                                                                <i title="BOM" class="fa fa-cogs"></i>

                                                                                                BOM

                                                                                            </a>

                                                                                        <?php } ?>

                                                                                    </div>

                                                                                </div>




                                                                                <div class="row px-3 detail-view-accordion">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <!-------Classification------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                        Classification
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Goods Type :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $type_name ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs"> Group :</p>
                                                                                                                    <p class="font-bold text-xs group-desc" title="Group : <?= $group_name ?>"><?= $group_name ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Availablity Check :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['availabilityCheck'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-------Basic Details------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        Basic Details
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Net Weight :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Gross Weight :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Volume :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['volume'] ?> m<sup>3</sup></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Height :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Width :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Length :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['length'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <?php
                                                                                        $item_id = $row['itemId'];
                                                                                        $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
                                                                                        $storage_data = $storage_sql['data'];


                                                                                        ?>

                                                                                        <!-------Storage Details------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#storageDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        Storage Details
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="storageDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <!-- <div class="display-flex-space-between">
                                            <p class="font-bold text-xs">Storage Bin :</p>
                                            <p class="font-bold text-xs"><?= $row['storageBin'] ?></p>
                                          </div>

                                          <div class="display-flex-space-between">
                                            <p class="font-bold text-xs">Picking Area :</p>
                                            <p class="font-bold text-xs"><?= $row['pickingArea'] ?></p>
                                          </div>

                                          <div class="display-flex-space-between">
                                            <p class="font-bold text-xs">Temp Control :</p>
                                            <p class="font-bold text-xs"><?= $row['tempControl'] ?></p>
                                          </div> -->

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Storage Control :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['storageControl'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Max Storage Period :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriod'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Max Storage Period Time :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriodTimeUnit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Min Remain Self Life Time Unit :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLife'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Min Remain Self Life :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLifeTimeUnit'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-------Purchase Details------>
                                                                                        <!-- <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                <div class="accordion-item">
                                  <h2 class="accordion-header" id="flush-headingOne">
                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#purchaseDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                      Storage Details
                                    </button>
                                  </h2>
                                  <div id="purchaseDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body p-0">

                                      <div class="card">

                                        <div class="card-body p-3">

                                          <div class="display-flex-space-between">
                                            <p class="font-bold text-xs">Purchasing Value Key :</p>
                                            <p class="font-bold text-xs"><?= $row['purchasingValueKey'] ?></p>
                                          </div>



                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div> -->

                                                                                    </div>








                                                                                </div>

                                                                            </div>



                                                                            <!-- -------------------Audit History Tab Body Start------------------------- -->

                                                                            <div class="tab-pane fade" id="history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['itemCode']) ?>">

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





                                            </table>

                                            <?php

                                            if ($count > 0 && $count > $GLOBALS['show']) {

                                            ?>

                                                <div class="pagination align-right">

                                                    <?php pagination($count, "frm_opts"); ?>

                                                </div>



                                                <!-- End .pagination -->



                                            <?php  } ?>



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

                                    <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />

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

                                                        Base UOM</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                                                        Group</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                                                        Type</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                                                        Moving Weighted Price</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                                                        Valuation Class</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                                                        Target Price</td>

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
<?php
} elseif (isset($_GET['fg'])) {


?>

    <div class="content-wrapper is-goods is-goods-fg">

        <!-- Content Header (Page header) -->



        <!-- Main content -->

        <section class="content">

            <div class="container-fluid">





                <!-- row -->

                <div class="row p-0 m-0">

                    <div class="col-12 mt-2 p-0">



                        <!-- <ol class="breadcrumb bg-transparent">

          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Items</a></li>


        </ol> -->

                        <div class="p-0 pt-1 my-2">

                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                    <h3 class="card-title">
                                        Item Master
                                    </h3>


                                    <a href="goods.php?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>

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

                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

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
                                                            <a href="goods.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                                                            <a href="goods-type-items.php" class="btn "><i class="fa fa-list mr-2 "></i>Raw Materials</a>
                                                            <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                                                            <a href="goods-type-items.php?fg" class="btn active"><i class="fa fa-lock-open mr-2 active"></i>FG</a>
                                                            <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                                                            <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                                                        </div>
                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="goods.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                                <a href="goods-type-items.php" class="btn"><i class="fa fa-list mr-2"></i>Raw Materials</a>
                                                                <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                                                                <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                                                                <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                                                                <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                                                            </div>
                                                        </div>

                                                        <div class="section serach-input-section">

                                                            <input type="text" id="myInput" name="keyword" placeholder="" class="field form-control" value="<?php echo $keywd; ?>" />

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

                                                <div class="col-lg-1 col-md-1 col-sm-1">

                                                    <a href="goods.php?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

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

                            <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                                        <?php

                                        $cond = '';




                                        $sts = " AND `status` !='deleted'";

                                        if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {

                                            $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                        }




                                        if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {

                                            $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                        }

                                        if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                            $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR `itemName` like '%" . $_REQUEST['keyword2'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword2'] . "%')";
                                        } else {

                                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                                                $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                                            }
                                        }




                                        $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . " AND `company_id`=$company_id AND (`goodsType`=3 OR `goodsType`=4 ) ORDER BY itemId desc  ";

                                        $qry_list = mysqli_query($dbCon, $sql_list);

                                        $num_list = mysqli_num_rows($qry_list);





                                        $countShow = "SELECT count(*) FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id AND (`goodsType`=3 OR `goodsType`=4)";

                                        $countQry = mysqli_query($dbCon, $countShow);

                                        $rowCount = mysqli_fetch_array($countQry);

                                        $count = $rowCount[0];

                                        $cnt = $GLOBALS['start'] + 1;

                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                                        $settingsCheckbox = unserialize($settingsCh);

                                        if ($num_list > 0) { ?>

                                            <table class="table defaultDataTable table-hover text-nowrap">

                                                <thead>

                                                    <tr class="alert-light">

                                                        <!-- <th>#</th> -->

                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                            <th>Item Code</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Item Name</th>

                                                        <?php }

                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Base UOM</th>

                                                        <?php  }

                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Group</th>

                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>

                                                            <th>Type</th>

                                                        <?php

                                                        }

                                                        if (in_array(6, $settingsCheckbox)) { ?>

                                                            <th>Moving Weighted Price</th>

                                                        <?php  }

                                                        if (in_array(7, $settingsCheckbox)) { ?>

                                                            <th>Valuation Class</th>

                                                        <?php

                                                        }

                                                        if (in_array(8, $settingsCheckbox)) { ?>

                                                            <th>Target Price</th>

                                                        <?php

                                                        }




                                                        ?>

                                                        <th>BOM Status</th>

                                                        <th>Status</th>

                                                        <th>Action</th>
                                                        <th>Add</th>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <?php

                                                    $customerModalHtml = "";

                                                    while ($row = mysqli_fetch_assoc($qry_list)) {
                                                        //console($row);
                                                        $itemId = $row['itemId'];
                                                        $itemCode = $row['itemCode'];

                                                        $itemName = $row['itemName'];

                                                        $netWeight = $row['netWeight'];

                                                        $volume = $row['volume'];

                                                        $goodsType = $row['goodsType'];

                                                        $grossWeight = $row['grossWeight'];

                                                        $buom_id = $row['baseUnitMeasure'];

                                                        $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                                                        $buom = $buom_sql['data']['uomName'];
                                                        //  console($buom);



                                                        $goodTypeId = $row['goodsType'];
                                                        $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                                                        $type_name = $type_sql['data']['goodTypeName'];



                                                        $goodGroupId = $row['goodsGroup'];
                                                        $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                                                        $group_name = $group_sql['data']['goodGroupName'];


                                                        $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                                                        $mwp = $summary_sql['data']['movingWeightedPrice'];
                                                        $val_class = $summary_sql['data']['priceType'];

                                                    ?>

                                                        <tr>

                                                            <!-- <td><?= $cnt++ ?></td> -->

                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                                <td><?= $row['itemCode'] ?></td>

                                                            <?php }

                                                            if (in_array(2, $settingsCheckbox)) { ?>

                                                                <td>
                                                                    <p class="pre-normal"><?= $row['itemName'] ?></p>
                                                                </td>

                                                            <?php }

                                                            if (in_array(3, $settingsCheckbox)) { ?>

                                                                <td><?= $buom ?> </td>

                                                            <?php }

                                                            if (in_array(4, $settingsCheckbox)) { ?>

                                                                <td><p class="pre-normal group-max-width"><?= $group_name ?></p></td>

                                                            <?php }
                                                            if (in_array(5, $settingsCheckbox)) { ?>

                                                                <td>
                                                                   <?= $type_name ?>
                                                                </td>

                                                            <?php }
                                                            if (in_array(6, $settingsCheckbox)) { ?>

                                                                <td><?= $mwp ?></td>

                                                            <?php }

                                                            if (in_array(7, $settingsCheckbox)) { ?>

                                                                <td><?= $val_class ?></td>

                                                            <?php }

                                                            if (in_array(8, $settingsCheckbox)) { ?>

                                                                <td><?= $summary_sql['data']['itemPrice'] ?></td>

                                                            <?php }

                                                            ?>



                                                            <td>

                                                                <?php

                                                                if ($row['isBomRequired'] == 1) {



                                                                    echo '<span class="status">Required</span>';
                                                                } else {

                                                                    echo '<span class="status-danger">Not Required</span>';
                                                                }


                                                                ?>

                                                            </td>



                                                            <td>

                                                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                                                                    <input type="hidden" name="id" value="<?php echo $row['itemId'] ?>">

                                                                    <input type="hidden" name="changeStatus" value="active_inactive">

                                                                    <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">

                                                                        <?php if ($row['status'] == "active") { ?>

                                                                            <span class="status"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } else if ($row['status'] == "inactive") { ?>

                                                                            <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } else if ($row['status'] == "draft") { ?>

                                                                            <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } ?>



                                                                    </button>

                                                                </form>

                                                            </td>

                                                            <td>



                                                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" class="btn btn-sm">

                                                                    <i class="fa fa-eye po-list-icon"></i>

                                                                </a>

                                                            </td>

                                                            <td>
                                                                <?php
                                                                $item_id = $row['itemId'];
                                                                $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE  `location_id`=$location_id  AND `itemId`=$item_id ", true);
                                                                if ($check_sql['status'] == "success") {

                                                                ?>
                                                                    <button class="btn btn-success" type="button">Added</button>

                                                                <?php

                                                                } else {

                                                                ?>


                                                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $row['itemId'] ?>">Add</button>
                                                                <?php
                                                                }

                                                                ?>
                                                            </td>

                                                        </tr>


                                                        <!-----add form modal start --->
                                                        <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <form method="POST" action="">
                                                                            <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                                                                            <input type="hidden" name="item_id" value="<?= $row['itemId'] ?>">

                                                                            <div class="row">

                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                        <div class="card-header">

                                                                                            <h4>Storage Details</h4>

                                                                                        </div>

                                                                                        <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                    <div class="row goods-info-form-view customer-info-form-view">









                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Storage Control</label>

                                                                                                                <input type="text" name="storageControl" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Max Storage Period</label>

                                                                                                                <input type="text" name="maxStoragePeriod" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                            <div class="form-input">
                                                                                                                <label class="label-hidden" for="">Min Time Unit</label>
                                                                                                                <select id="minTime" name="minTime" class="select2 form-control">
                                                                                                                    <option value="">Min Time Unit</option>
                                                                                                                    <option value="Day">Day</option>
                                                                                                                    <option value="Month">Month</option>
                                                                                                                    <option value="Hours">Hours</option>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Minimum Remain Self life</label>

                                                                                                                <input type="text" name="minRemainSelfLife" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                            <div class="form-input">
                                                                                                                <label class="label-hidden" for="">Max Time Unit</label>
                                                                                                                <select id="maxTime" name="maxTime" class="select2 form-control">
                                                                                                                    <option value="">Max Time Unit</option>
                                                                                                                    <option value="Day">Day</option>
                                                                                                                    <option value="Month">Month</option>
                                                                                                                    <option value="Hours">Hours</option>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                </div>




                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                        <div class="card-header">

                                                                                            <h4>Pricing and Discount

                                                                                                <span class="text-danger">*</span>

                                                                                            </h4>

                                                                                        </div>

                                                                                        <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                    <div class="row goods-info-form-view customer-info-form-view">

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Target price</label>

                                                                                                                <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Max Discount</label>

                                                                                                                <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                                                                                    </div>


                                                                                </div>





                                                                            </div>










                                                                        </form>

                                                                    </div>
                                                                    <div class="modal-body" style="height: 500px; overflow: auto;">
                                                                        <div class="card">

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!---end modal --->


                                                        <!-- right modal start here  -->

                                                        <div class="modal fade right goods-modal goods-type-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                <!--Content-->

                                                                <div class="modal-content">

                                                                    <!--Header-->

                                                                    <div class="modal-header pt-4">

                                                                        <div class="row item-specification-row">

                                                                            <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                <div class="item-img">

                                                                                    <img src="../../public/assets/img/image/goods-item-image.png" title="goods-iem-image" alt="goods_item_image">

                                                                                </div>

                                                                            </div>


                                                                            <div class="col-lg-8 col-md-8 col-sm-8">

                                                                                <div class="head-title">

                                                                                    <p class="heading lead">Item Name : <?= $itemName ?></p>

                                                                                    <p class="item-code">Item Code : <?= $itemCode ?></p>

                                                                                    <p class="item-desc">Description : <?= $row['itemDesc'] ?></p>

                                                                                </div>

                                                                            </div>

                                                                        </div>
                                                                        <div class="display-flex-space-between mt-4 mb-3">
                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="home<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="true">Info</a>
                                                                                </li>

                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item trailLi">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $row['itemCode']) ?>" href="#history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="false"><i class="fas fa-history" aria-hidden="true" style="color: #fff;"></i>Trail<a>
                                                                                </li>
                                                                                <!---------------------Audit History Button End--------------------------->
                                                                            </ul>



                                                                            <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                <?php $itemId = base64_encode($row['itemId']) ?>

                                                                                <form action="" method="POST">



                                                                                    <!-- <a href="goods.php?edit=<?= $itemId ?>" name="customerEditBtn">

                                    <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                  </a> -->

                                                                                    <a href="">

                                                                                        <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                    </a>

                                                                                    <a href="">

                                                                                        <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                    </a>

                                                                                </form>

                                                                            </div>

                                                                        </div>

                                                                    </div>



                                                                    <!--Body-->

                                                                    <div class="modal-body" style="padding: 0;">
                                                                        <div class="tab-content" id="myTabContent">
                                                                            <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                                <div class="row">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                        <?php if ($row['bomStatus'] != 0) { ?>

                                                                                            <a href="goods.php?bom=<?= $itemId; ?>" class="btn btn-primary float-right m-3" name="customerEditBtn">

                                                                                                <i title="BOM" class="fa fa-cogs"></i>

                                                                                                BOM

                                                                                            </a>

                                                                                        <?php } ?>

                                                                                    </div>

                                                                                </div>




                                                                                <div class="row px-3 detail-view-accordion">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <!-------Classification------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                        Classification
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Goods Type :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $type_name ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs"> Group :</p>
                                                                                                                    <p class="font-bold text-xs group-desc" title="Group : <?= $group_name ?>"><?= $group_name ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Availablity Check :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['availabilityCheck'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-------Basic Details------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        Basic Details
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Net Weight :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Gross Weight :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Volume :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['volume'] ?> m<sup>3</sup></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Height :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Width :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Length :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['length'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <?php
                                                                                        $item_id = $row['itemId'];
                                                                                        $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
                                                                                        $storage_data = $storage_sql['data'];


                                                                                        ?>

                                                                                        <!-------Storage Details------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#storageDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        Storage Details
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="storageDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <!-- <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Storage Bin :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['storageBin'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Picking Area :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['pickingArea'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Temp Control :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['tempControl'] ?></p>
                                                                                                            </div> -->

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Storage Control :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['storageControl'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Max Storage Period :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriod'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Max Storage Period Time :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriodTimeUnit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Min Remain Self Life Time Unit :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLife'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Min Remain Self Life :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLifeTimeUnit'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-------Purchase Details------>
                                                                                        <!-- <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                        <div class="accordion-item">
                                                                                        <h2 class="accordion-header" id="flush-headingOne">
                                                                                            <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#purchaseDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                            Storage Details
                                                                                            </button>
                                                                                        </h2>
                                                                                        <div id="purchaseDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                            <div class="accordion-body p-0">

                                                                                            <div class="card">

                                                                                                <div class="card-body p-3">

                                                                                                <div class="display-flex-space-between">
                                                                                                    <p class="font-bold text-xs">Purchasing Value Key :</p>
                                                                                                    <p class="font-bold text-xs"><?= $row['purchasingValueKey'] ?></p>
                                                                                                </div>



                                                                                                </div>
                                                                                            </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        </div>
                                                                                    </div> -->

                                                                                    </div>








                                                                                </div>

                                                                            </div>
                                                                            <!-- -------------------Audit History Tab Body Start--------------------------->
                                                                            <div class="tab-pane fade" id="history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['itemCode']) ?>">

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
                                                                            <!---------------------Audit History Tab Body End------------------------- -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!--/.Content-->
                                                            </div>
                                                        </div>
                                                        <!-- right modal end here  -->
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <?php
                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                            ?>
                                                <div class="pagination align-right">
                                                    <?php pagination($count, "frm_opts"); ?>
                                                </div>
                                                <!-- End .pagination -->
                                            <?php  } ?>



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

                                    <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />

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

                                                        Base UOM</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                                                        Group</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                                                        Type</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                                                        Moving Weighted Price</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                                                        Valuation Class</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                                                        Target Price</td>

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
} elseif (isset($_GET['service'])) {


?>

    <div class="content-wrapper is-goods is-goods-service">

        <!-- Content Header (Page header) -->



        <!-- Main content -->

        <section class="content">

            <div class="container-fluid">





                <!-- row -->

                <div class="row p-0 m-0">

                    <div class="col-12 mt-2 p-0">



                        <!-- <ol class="breadcrumb bg-transparent">

          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Items</a></li>


        </ol> -->

                        <div class="p-0 pt-1 my-2">

                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                    <h3 class="card-title">
                                        Item Master
                                    </h3>


                                    <a href="goods.php?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>

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

                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

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
                                                            <a href="goods.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                                                            <a href="goods-type-items.php" class="btn "><i class="fa fa-list mr-2 "></i>Raw Materials</a>
                                                            <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                                                            <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                                                            <a href="goods-type-items.php?service" class="btn active"><i class="fa fa-lock mr-2 active"></i>Services</a>
                                                            <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                                                        </div>

                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="goods.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                                <a href="goods-type-items.php" class="btn"><i class="fa fa-list mr-2"></i>Raw Materials</a>
                                                                <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                                                                <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                                                                <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                                                                <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                                                            </div>
                                                        </div>

                                                        <div class="section serach-input-section">

                                                            <input type="text" id="myInput" name="keyword" placeholder="" class="field form-control" value="<?php echo $keywd; ?>" />

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

                                                <div class="col-lg-1 col-md-1 col-sm-1">

                                                    <a href="goods.php?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

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

                            <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                                        <?php

                                        $cond = '';




                                        $sts = " AND `status` !='deleted'";

                                        if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {

                                            $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                        }




                                        if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {

                                            $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                        }

                                        if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                            $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR `itemName` like '%" . $_REQUEST['keyword2'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword2'] . "%')";
                                        } else {

                                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                                                $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                                            }
                                        }




                                        $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . " AND  (`goodsType`=5 OR `goodsType`=7 OR `goodsType`=10) AND `company_id`=$company_id  ORDER BY itemId desc  ";

                                        $qry_list = mysqli_query($dbCon, $sql_list);

                                        $num_list = mysqli_num_rows($qry_list);





                                        $countShow = "SELECT count(*) FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id AND  (`goodsType`=5 OR `goodsType`=7 OR `goodsType`=10) ";

                                        $countQry = mysqli_query($dbCon, $countShow);

                                        $rowCount = mysqli_fetch_array($countQry);

                                        $count = $rowCount[0];

                                        $cnt = $GLOBALS['start'] + 1;

                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                                        $settingsCheckbox = unserialize($settingsCh);

                                        if ($num_list > 0) { ?>

                                            <table class="table defaultDataTable table-hover text-nowrap">

                                                <thead>

                                                    <tr class="alert-light">

                                                        <!-- <th>#</th> -->

                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                            <th>Item Code</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Item Name</th>

                                                        <?php }



                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Group</th>

                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Type</th>

                                                        <?php

                                                        }

                                                        if (in_array(5, $settingsCheckbox)) { ?>

                                                            <th>Service Unit</th>


                                                        <?php  }

                                                        if (in_array(6, $settingsCheckbox)) { ?>

                                                            <th> HSN</th>

                                                        <?php

                                                        }

                                                        if (in_array(7, $settingsCheckbox)) { ?>

                                                            <th> TDS</th>

                                                        <?php

                                                        }






                                                        ?>

                                                        <!-- <th>BOM Status</th> -->

                                                        <th>Status</th>

                                                        <th>Action</th>
                                                        <th>Add</th>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <?php

                                                    $customerModalHtml = "";

                                                    while ($row = mysqli_fetch_assoc($qry_list)) {
                                                        //console($row);
                                                        $itemId = $row['itemId'];
                                                        $itemCode = $row['itemCode'];

                                                        $itemName = $row['itemName'];

                                                        $netWeight = $row['netWeight'];

                                                        $volume = $row['volume'];

                                                        $goodsType = $row['goodsType'];

                                                        $grossWeight = $row['grossWeight'];

                                                        $buom_id = $row['baseUnitMeasure'];

                                                        $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                                                        $buom = $buom_sql['data']['uomName'];
                                                        //  console($buom);



                                                        $goodTypeId = $row['goodsType'];
                                                        $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                                                        $type_name = $type_sql['data']['goodTypeName'];


                                                        $tds = $row['tds'];
                                                        $tds_sql = queryGet("SELECT * FROM `erp_tds_details` WHERE `id`=$tds ");
                                                        $tdsSec = $tds_sql['data']['section'];
                                                        $nature = $tds_sql['data']['natureOfTransaction'];


                                                        $service_unit_sql =  queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $row['service_unit'] . "' ");
                                                        $service_unit = $service_unit_sql['data']['uomName'];



                                                    ?>

                                                        <tr>

                                                            <!-- <td><?= $cnt++ ?></td> -->

                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                                <td><?= $row['itemCode'] ?></td>

                                                            <?php }

                                                            if (in_array(2, $settingsCheckbox)) { ?>

                                                                <td>
                                                                    <p class="pre-normal"><?= $row['itemName'] ?></p>
                                                                </td>

                                                            <?php }



                                                            if (in_array(3, $settingsCheckbox)) { ?>

                                                                <td>
                                                                    <p class="pre-normal group-max-width"><?= $group_name ?></p>
                                                                </td>

                                                            <?php }
                                                            if (in_array(4, $settingsCheckbox)) { ?>

                                                                <td><?= $type_name ?></td>

                                                            <?php }
                                                            if (in_array(5, $settingsCheckbox)) { ?>

                                                                <td><?= $service_unit ?></td>

                                                            <?php }

                                                            if (in_array(6, $settingsCheckbox)) { ?>

                                                                <td><?= $row['hsnCode'] ?></td>

                                                                <?php }




                                                            if (in_array(7, $settingsCheckbox)) {
                                                                if (isset($tdsSec)) { ?>

                                                                    <td>
                                                                        <p class="pre-wrap"><?= $tdsSec . "(" . $nature . ")" ?></p>
                                                                    </td>

                                                                <?php } else {
                                                                ?>
                                                                    <td>-</td>
                                                            <?php
                                                                }
                                                            }

                                                            ?>



                                                            <!-- <td>

                                                            <?php

                                                            if ($row['bomStatus'] == 1) {

                                                                if ($goodsBomController->isBomCreated($row['itemId'])) {

                                                                    echo '<span class="status">Created</span>';
                                                                } else {

                                                                    echo '<span class="status-warning">Not Created</span>';
                                                                }
                                                            } else {

                                                                echo '<span class="status-danger">Not Required</span>';
                                                            }

                                                            ?> -->

                                                            </td>



                                                            <td>

                                                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                                                                    <input type="hidden" name="id" value="<?php echo $row['itemId'] ?>">

                                                                    <input type="hidden" name="changeStatus" value="active_inactive">

                                                                    <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">

                                                                        <?php if ($row['status'] == "active") { ?>

                                                                            <span class="status"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } else if ($row['status'] == "inactive") { ?>

                                                                            <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } else if ($row['status'] == "draft") { ?>

                                                                            <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } ?>



                                                                    </button>

                                                                </form>

                                                            </td>

                                                            <td>



                                                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" class="btn btn-sm">

                                                                    <i class="fa fa-eye po-list-icon"></i>

                                                                </a>

                                                            </td>

                                                            <td>
                                                                <?php
                                                                $item_id = $row['itemId'];
                                                                $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE  `location_id`=$location_id  AND `itemId`=$item_id ", true);
                                                                if ($check_sql['status'] == "success") {

                                                                ?>
                                                                    <button class="btn btn-success" type="button">Added</button>

                                                                <?php

                                                                } else {

                                                                ?>


                                                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $row['itemId'] ?>">Add</button>
                                                                <?php
                                                                }

                                                                ?>
                                                            </td>

                                                        </tr>


                                                        <!-----add form modal start --->
                                                        <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <form method="POST" action="">
                                                                            <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                                                                            <input type="hidden" name="item_id" value="<?= $row['itemId'] ?>">

                                                                            <div class="row">

                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                        <div class="card-header">

                                                                                            <h4>Storage Details</h4>

                                                                                        </div>

                                                                                        <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                    <div class="row goods-info-form-view customer-info-form-view">









                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Storage Control</label>

                                                                                                                <input type="text" name="storageControl" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Max Storage Period</label>

                                                                                                                <input type="text" name="maxStoragePeriod" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                            <div class="form-input">
                                                                                                                <label class="label-hidden" for="">Min Time Unit</label>
                                                                                                                <select id="minTime" name="minTime" class="select2 form-control">
                                                                                                                    <option value="">Min Time Unit</option>
                                                                                                                    <option value="Day">Day</option>
                                                                                                                    <option value="Month">Month</option>
                                                                                                                    <option value="Hours">Hours</option>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Minimum Remain Self life</label>

                                                                                                                <input type="text" name="minRemainSelfLife" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                            <div class="form-input">
                                                                                                                <label class="label-hidden" for="">Max Time Unit</label>
                                                                                                                <select id="maxTime" name="maxTime" class="select2 form-control">
                                                                                                                    <option value="">Max Time Unit</option>
                                                                                                                    <option value="Day">Day</option>
                                                                                                                    <option value="Month">Month</option>
                                                                                                                    <option value="Hours">Hours</option>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                </div>




                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                        <div class="card-header">

                                                                                            <h4>Pricing and Discount

                                                                                                <span class="text-danger">*</span>

                                                                                            </h4>

                                                                                        </div>

                                                                                        <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                    <div class="row goods-info-form-view customer-info-form-view">

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Target price</label>

                                                                                                                <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Max Discount</label>

                                                                                                                <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                                                                                    </div>


                                                                                </div>





                                                                            </div>










                                                                        </form>

                                                                    </div>
                                                                    <div class="modal-body" style="height: 500px; overflow: auto;">
                                                                        <div class="card">

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!---end modal --->


                                                        <!-- right modal start here  -->

                                                        <div class="modal fade right goods-modal goods-type-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                <!--Content-->

                                                                <div class="modal-content">

                                                                    <!--Header-->

                                                                    <div class="modal-header pt-4">

                                                                        <div class="row item-specification-row">

                                                                            <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                <div class="item-img">

                                                                                    <img src="../../public/assets/img/image/goods-item-image.png" title="goods-iem-image" alt="goods_item_image">

                                                                                </div>

                                                                            </div>

                                                                            <div class="col-lg-8 col-md-8 col-sm-8">

                                                                                <div class="head-title">

                                                                                    <p class="heading lead">Item Name : <?= $itemName ?></p>

                                                                                    <p class="item-code">Item Code : <?= $itemCode ?></p>

                                                                                    <p class="item-desc">Description : <?= $row['itemDesc'] ?></p>

                                                                                </div>

                                                                            </div>

                                                                        </div>

                                                                        <div class="display-flex-space-between mt-4 mb-3">
                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="home<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="true">Info</a>
                                                                                </li>

                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" data-ccode="<?= $row['ItemCode'] ?>" href="#history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="false"><i class="fas fa-history mr-2"></i>Trail</a>
                                                                                </li>
                                                                                <!---------------------Audit History Button End--------------------------->
                                                                            </ul>


                                                                            <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                <?php $itemId = base64_encode($row['itemId']) ?>

                                                                                <form action="" method="POST">

                                                                                    <a href="">

                                                                                        <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                    </a>

                                                                                    <a href="">

                                                                                        <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                    </a>

                                                                                </form>

                                                                            </div>

                                                                        </div>

                                                                    </div>


                                                                    <!--Body-->

                                                                    <div class="modal-body" style="padding: 0;">

                                                                        <div class="tab-content" id="myTabContent">

                                                                            <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="home-tab">

                                                                                <div class="row">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                        <?php if ($row['bomStatus'] != 0) { ?>

                                                                                            <a href="goods.php?bom=<?= $itemId; ?>" class="btn btn-primary float-right m-3" name="customerEditBtn">

                                                                                                <i title="BOM" class="fa fa-cogs"></i>

                                                                                                BOM

                                                                                            </a>

                                                                                        <?php } ?>

                                                                                    </div>

                                                                                </div>




                                                                                <div class="row px-3 detail-view-accordion">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <!-------Classification------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                        Classification
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Goods Type :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $type_name ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs"> Group :</p>
                                                                                                                    <p class="font-bold text-xs group-desc" title="Group : <?= $group_name ?>"><?= $group_name ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Availablity Check :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['availabilityCheck'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-------Basic Details------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        Basic Details
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Net Weight :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Gross Weight :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Volume :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['volume'] ?> m<sup>3</sup></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Height :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Width :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Length :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['length'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <?php
                                                                                        $item_id = $row['itemId'];
                                                                                        $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
                                                                                        $storage_data = $storage_sql['data'];


                                                                                        ?>

                                                                                        <!-------Storage Details------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#storageDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        Storage Details
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="storageDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <!-- <div class="display-flex-space-between">
                                                    <p class="font-bold text-xs">Storage Bin :</p>
                                                    <p class="font-bold text-xs"><?= $row['storageBin'] ?></p>
                                                  </div>

                                                  <div class="display-flex-space-between">
                                                    <p class="font-bold text-xs">Picking Area :</p>
                                                    <p class="font-bold text-xs"><?= $row['pickingArea'] ?></p>
                                                  </div>

                                                  <div class="display-flex-space-between">
                                                    <p class="font-bold text-xs">Temp Control :</p>
                                                    <p class="font-bold text-xs"><?= $row['tempControl'] ?></p>
                                                  </div> -->

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Storage Control :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['storageControl'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Max Storage Period :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriod'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Max Storage Period Time :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriodTimeUnit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Min Remain Self Life Time Unit :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLife'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Min Remain Self Life :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLifeTimeUnit'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-------Purchase Details------>
                                                                                        <!-- <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                        <div class="accordion-item">
                                          <h2 class="accordion-header" id="flush-headingOne">
                                            <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#purchaseDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                              Storage Details
                                            </button>
                                          </h2>
                                          <div id="purchaseDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body p-0">

                                              <div class="card">

                                                <div class="card-body p-3">

                                                  <div class="display-flex-space-between">
                                                    <p class="font-bold text-xs">Purchasing Value Key :</p>
                                                    <p class="font-bold text-xs"><?= $row['purchasingValueKey'] ?></p>
                                                  </div>



                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div> -->

                                                                                    </div>








                                                                                </div>

                                                                            </div>

                                                                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                                                                <?= $itemName ?>

                                                                            </div>
                                                                            <!-- -------------------Audit History Tab Body Start------------------------- -->

                                                                            <div class="tab-pane fade" id="history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['itemCode']) ?>">

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





                                            </table>

                                            <?php

                                            if ($count > 0 && $count > $GLOBALS['show']) {

                                            ?>

                                                <div class="pagination align-right">

                                                    <?php pagination($count, "frm_opts"); ?>

                                                </div>



                                                <!-- End .pagination -->



                                            <?php  } ?>



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

                                    <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />

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

                                                        Group</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                                                        Type</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                                                        Service Unit</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                                                        HSN Code</td>

                                                </tr>
                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                                                        TDS</td>

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
} elseif (isset($_GET['asset'])) {

?>


    <div class="content-wrapper is-goods is-goods-asset">

        <!-- Content Header (Page header) -->



        <!-- Main content -->

        <section class="content">

            <div class="container-fluid">





                <!-- row -->

                <div class="row p-0 m-0">

                    <div class="col-12 mt-2 p-0">



                        <!-- <ol class="breadcrumb bg-transparent">

  <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

  <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Items</a></li>


</ol> -->

                        <div class="p-0 pt-1 my-2">

                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                    <h3 class="card-title">
                                        Item Master
                                    </h3>


                                    <a href="goods.php?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>

                                </li>

                            </ul>

                        </div>

                        <div class="filter-list">
                            <a href="goods.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                            <a href="goods-type-items.php" class="btn"><i class="fa fa-list mr-2"></i>Raw Materials</a>
                            <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                            <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                            <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                            <a href="manage-assets.php" class="btn active"><i class="fa fa-lock mr-2 active"></i>Assets</a>
                        </div>

                        <?php
                        $keywd = '';
                        if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                            $keywd = $_REQUEST['keyword'];
                        } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                            $keywd = $_REQUEST['keyword2'];
                        } ?>


                        <div class="card card-tabs" style="border-radius: 20px;">

                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                                <div class="card-body">

                                    <div class="row filter-serach-row">

                                        <div class="col-lg-2 col-md-2 col-sm-12">

                                            <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        </div>

                                        <div class="col-lg-10 col-md-10 col-sm-12">

                                            <div class="row table-header-item">

                                                <div class="col-lg-11 col-md-11 col-sm-11">

                                                    <div class="section serach-input-section">

                                                        <input type="text" id="myInput" name="keyword" placeholder="" class="field form-control" value="<?php echo $keywd; ?>" />

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

                                                <div class="col-lg-1 col-md-1 col-sm-1">

                                                    <a href="goods.php?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                                                </div>

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
                            <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                                    <?php

                                    $cond = '';




                                    $sts = " AND `status` !='deleted'";

                                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {

                                        $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                    }




                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {

                                        $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }

                                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                        $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR `itemName` like '%" . $_REQUEST['keyword2'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword2'] . "%')";
                                    } else {

                                        if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                                            $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                                        }
                                    }




                                    $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . " AND  `goodsType`=9 AND `company_id`=$company_id  ORDER BY itemId desc  ";

                                    $qry_list = mysqli_query($dbCon, $sql_list);

                                    $num_list = mysqli_num_rows($qry_list);





                                    $countShow = "SELECT count(*) FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id AND `goodsType`=9 ";

                                    $countQry = mysqli_query($dbCon, $countShow);

                                    $rowCount = mysqli_fetch_array($countQry);

                                    $count = $rowCount[0];

                                    $cnt = $GLOBALS['start'] + 1;

                                    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                                    $settingsCheckbox = unserialize($settingsCh);

                                    if ($num_list > 0) { ?>

                                        <table class="table defaultDataTable table-hover text-nowrap">

                                            <thead>

                                                <tr class="alert-light">

                                                    <!-- <th>#</th> -->

                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                        <th>Item Code</th>

                                                    <?php }

                                                    if (in_array(2, $settingsCheckbox)) { ?>

                                                        <th>Item Name</th>

                                                    <?php }

                                                    if (in_array(3, $settingsCheckbox)) { ?>

                                                        <th>Base UOM</th>

                                                    <?php  }

                                                    if (in_array(4, $settingsCheckbox)) { ?>

                                                        <th>Group</th>

                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>

                                                        <th>Type</th>

                                                    <?php

                                                    }

                                                    if (in_array(6, $settingsCheckbox)) { ?>

                                                        <th>Moving Weighted Price</th>

                                                    <?php  }

                                                    if (in_array(7, $settingsCheckbox)) { ?>

                                                        <th>Valuation Class</th>

                                                    <?php

                                                    }




                                                    if (in_array(8, $settingsCheckbox)) { ?>

                                                        <th>Target Price</th>

                                                    <?php

                                                    }





                                                    ?>

                                                    <th>BOM Status</th>

                                                    <th>Status</th>

                                                    <th>Action</th>
                                                    <th>Add</th>

                                                </tr>

                                            </thead>

                                            <tbody>

                                                <?php

                                                $customerModalHtml = "";

                                                while ($row = mysqli_fetch_assoc($qry_list)) {
                                                    //console($row);
                                                    $itemId = $row['itemId'];
                                                    $itemCode = $row['itemCode'];

                                                    $itemName = $row['itemName'];

                                                    $netWeight = $row['netWeight'];

                                                    $volume = $row['volume'];

                                                    $goodsType = $row['goodsType'];

                                                    $grossWeight = $row['grossWeight'];

                                                    $buom_id = $row['baseUnitMeasure'];

                                                    $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                                                    $buom = $buom_sql['data']['uomName'];
                                                    //  console($buom);



                                                    $goodTypeId = $row['goodsType'];
                                                    $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                                                    $type_name = $type_sql['data']['goodTypeName'];



                                                    $goodGroupId = $row['goodsGroup'];
                                                    $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                                                    $group_name = $group_sql['data']['goodGroupName'];


                                                    $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                                                    $mwp = $summary_sql['data']['movingWeightedPrice'];
                                                    $val_class = $summary_sql['data']['priceType'];

                                                ?>

                                                    <tr>

                                                        <!-- <td><?= $cnt++ ?></td> -->

                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                            <td><?= $row['itemCode'] ?></td>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <td>
                                                                <p class="pre-normal"><?= $row['itemName'] ?></p>
                                                            </td>

                                                        <?php }

                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <td><?= $buom ?> </td>

                                                        <?php }

                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <td>
                                                                <p class="pre-normal group-max-width"><?= $group_name ?></p>
                                                            </td>

                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>

                                                            <td><?= $type_name ?></td>

                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>

                                                            <td><?= $mwp ?></td>

                                                        <?php }

                                                        if (in_array(7, $settingsCheckbox)) { ?>

                                                            <td><?= $val_class ?></td>

                                                        <?php }


                                                        if (in_array(8, $settingsCheckbox)) { ?>

                                                            <td><?= $summary_sql['data']['itemPrice']
                                                                ?></td>

                                                        <?php }

                                                        ?>



                                                        <td>

                                                            <?php

                                                            if ($row['bomStatus'] == 1) {

                                                                if ($goodsBomController->isBomCreated($row['itemId'])) {

                                                                    echo '<span class="status">Created</span>';
                                                                } else {

                                                                    echo '<span class="status-warning">Not Created</span>';
                                                                }
                                                            } else {

                                                                echo '<span class="status-danger">Not Required</span>';
                                                            }

                                                            ?>

                                                        </td>



                                                        <td>

                                                            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                                                                <input type="hidden" name="id" value="<?php echo $row['itemId'] ?>">

                                                                <input type="hidden" name="changeStatus" value="active_inactive">

                                                                <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">

                                                                    <?php if ($row['status'] == "active") { ?>

                                                                        <span class="status"><?php echo ucfirst($row['status']); ?></span>

                                                                    <?php } else if ($row['status'] == "inactive") { ?>

                                                                        <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>

                                                                    <?php } else if ($row['status'] == "draft") { ?>

                                                                        <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>

                                                                    <?php } ?>



                                                                </button>

                                                            </form>

                                                        </td>

                                                        <td>



                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" class="btn btn-sm">

                                                                <i class="fa fa-eye po-list-icon"></i>

                                                            </a>

                                                        </td>

                                                        <td>
                                                            <?php
                                                            $item_id = $row['itemId'];
                                                            $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE  `location_id`=$location_id  AND `itemId`=$item_id ", true);
                                                            if ($check_sql['status'] == "success") {

                                                            ?>
                                                                <button class="btn btn-success" type="button">Added</button>

                                                            <?php

                                                            } else {

                                                            ?>


                                                                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $row['itemId'] ?>">Add</button>
                                                            <?php
                                                            }

                                                            ?>
                                                        </td>

                                                    </tr>


                                                    <!-----add form modal start --->
                                                    <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <form method="POST" action="">
                                                                        <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                                                                        <input type="hidden" name="item_id" value="<?= $row['itemId'] ?>">

                                                                        <div class="row">

                                                                            <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                    <div class="card-header">

                                                                                        <h4>Storage Details</h4>

                                                                                    </div>

                                                                                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                        <div class="row">

                                                                                            <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                <div class="row goods-info-form-view customer-info-form-view">









                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                        <div class="form-input">

                                                                                                            <label for="">Storage Control</label>

                                                                                                            <input type="text" name="storageControl" class="form-control">

                                                                                                        </div>

                                                                                                    </div>

                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                        <div class="form-input">

                                                                                                            <label for="">Max Storage Period</label>

                                                                                                            <input type="text" name="maxStoragePeriod" class="form-control">

                                                                                                        </div>

                                                                                                    </div>

                                                                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                        <div class="form-input">
                                                                                                            <label class="label-hidden" for="">Min Time Unit</label>
                                                                                                            <select id="minTime" name="minTime" class="select2 form-control">
                                                                                                                <option value="">Min Time Unit</option>
                                                                                                                <option value="Day">Day</option>
                                                                                                                <option value="Month">Month</option>
                                                                                                                <option value="Hours">Hours</option>

                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                        <div class="form-input">

                                                                                                            <label for="">Minimum Remain Self life</label>

                                                                                                            <input type="text" name="minRemainSelfLife" class="form-control">

                                                                                                        </div>

                                                                                                    </div>

                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                        <div class="form-input">
                                                                                                            <label class="label-hidden" for="">Max Time Unit</label>
                                                                                                            <select id="maxTime" name="maxTime" class="select2 form-control">
                                                                                                                <option value="">Max Time Unit</option>
                                                                                                                <option value="Day">Day</option>
                                                                                                                <option value="Month">Month</option>
                                                                                                                <option value="Hours">Hours</option>

                                                                                                            </select>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                </div>

                                                                            </div>




                                                                            <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                    <div class="card-header">

                                                                                        <h4>Pricing and Discount

                                                                                            <span class="text-danger">*</span>

                                                                                        </h4>

                                                                                    </div>

                                                                                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                        <div class="row">

                                                                                            <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                <div class="row goods-info-form-view customer-info-form-view">

                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                        <div class="form-input">

                                                                                                            <label for="">Target price</label>

                                                                                                            <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                                                                        </div>

                                                                                                    </div>

                                                                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                        <div class="form-input">

                                                                                                            <label for="">Max Discount</label>

                                                                                                            <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                </div>

                                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                    <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                                                                                </div>


                                                                            </div>





                                                                        </div>










                                                                    </form>

                                                                </div>
                                                                <div class="modal-body" style="height: 500px; overflow: auto;">
                                                                    <div class="card">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <!---end modal --->


                                                    <!-- right modal start here  -->

                                                    <div class="modal fade right goods-modal goods-type-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                            <!--Content-->

                                                            <div class="modal-content">

                                                                <!--Header-->

                                                                <div class="modal-header pt-4">

                                                                    <div class="row item-specification-row">

                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                            <div class="item-img">

                                                                                <img src="../../public/assets/img/image/goods-item-image.png" title="goods-iem-image" alt="goods_item_image">

                                                                            </div>

                                                                        </div>

                                                                        <div class="col-lg-8 col-md-8 col-sm-8">

                                                                            <div class="head-title">

                                                                                <p class="heading lead">Item Name : <?= $itemName ?></p>

                                                                                <p class="item-code">Item Code : <?= $itemCode ?></p>

                                                                                <p class="item-desc">Description : <?= $row['itemDesc'] ?></p>

                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                    <div class="display-flex-space-between mt-4 mb-3">
                                                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                            <li class="nav-item">
                                                                                <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="home<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="true">Info</a>
                                                                            </li>

                                                                            <!-- -------------------Audit History Button Start------------------------- -->
                                                                            <li class="nav-item">
                                                                                <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" data-ccode="<?= $row['ItemCode'] ?>" href="#history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="false"><i class="fas fa-history mr-2"></i>Trail</a>
                                                                            </li>
                                                                            <!---------------------Audit History Button End--------------------------->
                                                                        </ul>
                                                                        <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                            <?php $itemId = base64_encode($row['itemId']) ?>

                                                                            <form action="" method="POST">




                                                                                <a href="">

                                                                                    <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                </a>

                                                                                <a href="">

                                                                                    <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                </a>

                                                                            </form>

                                                                        </div>

                                                                    </div>

                                                                </div>



                                                                <!--Body-->

                                                                <div class="modal-body" style="padding: 0;">







                                                                    <div class="tab-content" id="myTabContent">



                                                                        <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                            <div class="row">

                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <?php if ($row['bomStatus'] != 0) { ?>

                                                                                        <a href="goods.php?bom=<?= $itemId; ?>" class="btn btn-primary float-right m-3" name="customerEditBtn">

                                                                                            <i title="BOM" class="fa fa-cogs"></i>

                                                                                            BOM

                                                                                        </a>

                                                                                    <?php } ?>

                                                                                </div>

                                                                            </div>




                                                                            <div class="row px-3 detail-view-accordion">

                                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                    <!-------Classification------>
                                                                                    <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                        <div class="accordion-item">
                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                    Classification
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                <div class="accordion-body p-0">

                                                                                                    <div class="card">

                                                                                                        <div class="card-body p-3">

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Goods Type :</p>
                                                                                                                <p class="font-bold text-xs"><?= $type_name ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs"> Group :</p>
                                                                                                                <p class="font-bold text-xs group-desc" title="Group : <?= $group_name ?>"><?= $group_name ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Availablity Check :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['availabilityCheck'] ?></p>
                                                                                                            </div>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-------Basic Details------>
                                                                                    <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                        <div class="accordion-item">
                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                    Basic Details
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                <div class="accordion-body p-0">

                                                                                                    <div class="card">

                                                                                                        <div class="card-body p-3">

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Net Weight :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Gross Weight :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Volume :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['volume'] ?> m<sup>3</sup></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Height :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Width :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Length :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['length'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                            </div>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <?php
                                                                                    $item_id = $row['itemId'];
                                                                                    $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
                                                                                    $storage_data = $storage_sql['data'];


                                                                                    ?>

                                                                                    <!-------Storage Details------>
                                                                                    <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                        <div class="accordion-item">
                                                                                            <h2 class="accordion-header" id="flush-headingOne">
                                                                                                <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#storageDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                    Storage Details
                                                                                                </button>
                                                                                            </h2>
                                                                                            <div id="storageDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                <div class="accordion-body p-0">

                                                                                                    <div class="card">

                                                                                                        <div class="card-body p-3">

                                                                                                            <!-- <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Storage Bin :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['storageBin'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Picking Area :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['pickingArea'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Temp Control :</p>
                                                                                                                <p class="font-bold text-xs"><?= $row['tempControl'] ?></p>
                                                                                                            </div> -->

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Storage Control :</p>
                                                                                                                <p class="font-bold text-xs"><?= $storage_data['storageControl'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Max Storage Period :</p>
                                                                                                                <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriod'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Max Storage Period Time :</p>
                                                                                                                <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriodTimeUnit'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Min Remain Self Life Time Unit :</p>
                                                                                                                <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLife'] ?></p>
                                                                                                            </div>

                                                                                                            <div class="display-flex-space-between">
                                                                                                                <p class="font-bold text-xs">Min Remain Self Life :</p>
                                                                                                                <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLifeTimeUnit'] ?></p>
                                                                                                            </div>

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <!-------Purchase Details------>
                                                                                    <!-- <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                <div class="accordion-item">
                                  <h2 class="accordion-header" id="flush-headingOne">
                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#purchaseDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                      Storage Details
                                    </button>
                                  </h2>
                                  <div id="purchaseDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body p-0">

                                      <div class="card">

                                        <div class="card-body p-3">

                                          <div class="display-flex-space-between">
                                            <p class="font-bold text-xs">Purchasing Value Key :</p>
                                            <p class="font-bold text-xs"><?= $row['purchasingValueKey'] ?></p>
                                          </div>



                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div> -->

                                                                                </div>








                                                                            </div>

                                                                        </div>

                                                                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                                                            <?= $itemName ?>

                                                                        </div>
                                                                        <!-- -------------------Audit History Tab Body Start------------------------- -->

                                                                        <div class="tab-pane fade" id="history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                            <div class="audit-head-section mb-3 mt-3 ">
                                                                                <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                                                                <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['itemCode']) ?>">

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





                                        </table>

                                        <?php

                                        if ($count > 0 && $count > $GLOBALS['show']) {

                                        ?>

                                            <div class="pagination align-right">

                                                <?php pagination($count, "frm_opts"); ?>

                                            </div>



                                            <!-- End .pagination -->



                                        <?php  } ?>



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

                                            <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />

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

                                                                Base UOM</td>

                                                        </tr>

                                                        <tr>

                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                                                                Group</td>

                                                        </tr>

                                                        <tr>

                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                                                                Type</td>

                                                        </tr>

                                                        <tr>

                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                                                                Moving Weighted Price</td>

                                                        </tr>

                                                        <tr>

                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                                                                Valuation Class</td>

                                                        </tr>

                                                        <tr>

                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                                                                Target Price</td>

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
} else {

?>

    <div class="content-wrapper is-goods">

        <!-- Content Header (Page header) -->



        <!-- Main content -->

        <section class="content">

            <div class="container-fluid">





                <!-- row -->

                <div class="row p-0 m-0">

                    <div class="col-12 mt-2 p-0">



                        <!-- <ol class="breadcrumb bg-transparent">

          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Items</a></li>


        </ol> -->

                        <div class="p-0 pt-1 my-2">

                            <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                    <h3 class="card-title">
                                        Item Master
                                    </h3>


                                    <a href="goods.php?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>

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

                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

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
                                                            <a href="goods.php" class="btn"><i class="fa fa-stream mr-2"></i>All</a>
                                                            <a href="goods-type-items.php" class="btn active"><i class="fa fa-list mr-2 active"></i>Raw Materials</a>
                                                            <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                                                            <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                                                            <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                                                            <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                                                        </div>
                                                        <div class="dropdown filter-dropdown" id="filterDropdown">

                                                            <button type="button" class="dropbtn" id="dropBtn">
                                                                <i class="fas fa-filter po-list-icon"></i>
                                                            </button>

                                                            <div class="dropdown-content">
                                                                <a href="goods.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
                                                                <a href="goods-type-items.php" class="btn"><i class="fa fa-list mr-2"></i>Raw Materials</a>
                                                                <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
                                                                <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
                                                                <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
                                                                <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
                                                            </div>
                                                        </div>

                                                        <div class="section serach-input-section">

                                                            <input type="text" id="myInput" name="keyword" placeholder="" class="field form-control" value="<?php echo $keywd; ?>" />

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

                                                <div class="col-lg-1 col-md-1 col-sm-1">

                                                    <a href="goods.php?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

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


                            <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                                        <?php

                                        $cond = '';




                                        $sts = " AND `status` !='deleted'";

                                        if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {

                                            $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                        }




                                        if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {

                                            $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                        }

                                        if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                            $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR `itemName` like '%" . $_REQUEST['keyword2'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword2'] . "%')";
                                        } else {

                                            if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                                                $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                                            }
                                        }




                                        $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . " AND  `goodsType`=1 AND `company_id`=$company_id  ORDER BY itemId desc  ";

                                        $qry_list = mysqli_query($dbCon, $sql_list);

                                        $num_list = mysqli_num_rows($qry_list);





                                        $countShow = "SELECT count(*) FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id AND `goodsType`=1 ";

                                        $countQry = mysqli_query($dbCon, $countShow);

                                        $rowCount = mysqli_fetch_array($countQry);

                                        $count = $rowCount[0];

                                        $cnt = $GLOBALS['start'] + 1;

                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                                        $settingsCheckbox = unserialize($settingsCh);

                                        if ($num_list > 0) { ?>

                                            <table class="table defaultDataTable table-hover text-nowrap">

                                                <thead>

                                                    <tr class="alert-light">

                                                        <!-- <th>#</th> -->

                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                            <th>Item Code</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th>Item Name</th>

                                                        <?php }

                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>Base UOM</th>

                                                        <?php  }

                                                        if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Group</th>

                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>

                                                            <th>Type</th>

                                                        <?php

                                                        }

                                                        if (in_array(6, $settingsCheckbox)) { ?>

                                                            <th>Moving Weighted Price</th>

                                                        <?php  }

                                                        if (in_array(7, $settingsCheckbox)) { ?>

                                                            <th>Valuation Class</th>

                                                        <?php

                                                        }

                                                        if (in_array(8, $settingsCheckbox)) { ?>

                                                            <th> Target Price</th>

                                                        <?php

                                                        }




                                                        ?>

                                                        <th>BOM Status</th>

                                                        <th>Status</th>

                                                        <th>Action</th>
                                                        <th>Add</th>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <?php

                                                    $customerModalHtml = "";

                                                    while ($row = mysqli_fetch_assoc($qry_list)) {
                                                        //console($row);
                                                        $itemId = $row['itemId'];
                                                        $itemCode = $row['itemCode'];

                                                        $itemName = $row['itemName'];

                                                        $netWeight = $row['netWeight'];

                                                        $volume = $row['volume'];

                                                        $goodsType = $row['goodsType'];

                                                        $grossWeight = $row['grossWeight'];

                                                        $buom_id = $row['baseUnitMeasure'];

                                                        $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                                                        $buom = $buom_sql['data']['uomName'];
                                                        //  console($buom);



                                                        $goodTypeId = $row['goodsType'];
                                                        $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                                                        $type_name = $type_sql['data']['goodTypeName'];



                                                        $goodGroupId = $row['goodsGroup'];
                                                        $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                                                        $group_name = $group_sql['data']['goodGroupName'];


                                                        $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                                                        $mwp = $summary_sql['data']['movingWeightedPrice'];
                                                        $val_class = $summary_sql['data']['priceType'];

                                                    ?>

                                                        <tr>

                                                            <!-- <td><?= $cnt++ ?></td> -->

                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>

                                                                <td><?= $row['itemCode'] ?></td>

                                                            <?php }

                                                            if (in_array(2, $settingsCheckbox)) { ?>

                                                                <td>
                                                                    <p class="pre-normal"><?= $row['itemName'] ?></p>
                                                                </td>

                                                            <?php }

                                                            if (in_array(3, $settingsCheckbox)) { ?>

                                                                <td><?= $buom ?> </td>

                                                            <?php }

                                                            if (in_array(4, $settingsCheckbox)) { ?>

                                                                <td>
                                                                    <p class="pre-normal group-max-width"><?= $group_name ?></p>
                                                                </td>

                                                            <?php }
                                                            if (in_array(5, $settingsCheckbox)) { ?>

                                                                <td><?= $type_name ?></td>

                                                            <?php }
                                                            if (in_array(6, $settingsCheckbox)) { ?>

                                                                <td><?= $mwp ?></td>

                                                            <?php }

                                                            if (in_array(7, $settingsCheckbox)) { ?>

                                                                <td><?= $val_class ?></td>

                                                            <?php }


                                                            if (in_array(8, $settingsCheckbox)) { ?>

                                                                <td><?= $summary_sql['data']['itemPrice']; ?></td>

                                                            <?php }


                                                            ?>



                                                            <td>

                                                                <?php

                                                                if ($row['bomStatus'] == 1) {

                                                                    if ($goodsBomController->isBomCreated($row['itemId'])) {

                                                                        echo '<span class="status">Created</span>';
                                                                    } else {

                                                                        echo '<span class="status-warning">Not Created</span>';
                                                                    }
                                                                } else {

                                                                    echo '<span class="status-danger">Not Required</span>';
                                                                }

                                                                ?>

                                                            </td>



                                                            <td>

                                                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                                                                    <input type="hidden" name="id" value="<?php echo $row['itemId'] ?>">

                                                                    <input type="hidden" name="changeStatus" value="active_inactive">

                                                                    <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">

                                                                        <?php if ($row['status'] == "active") { ?>

                                                                            <span class="status"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } else if ($row['status'] == "inactive") { ?>

                                                                            <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } else if ($row['status'] == "draft") { ?>

                                                                            <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>

                                                                        <?php } ?>



                                                                    </button>

                                                                </form>

                                                            </td>

                                                            <td>



                                                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" class="btn btn-sm">

                                                                    <i class="fa fa-eye po-list-icon"></i>

                                                                </a>

                                                            </td>

                                                            <td>
                                                                <?php
                                                                $item_id = $row['itemId'];
                                                                $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE  `location_id`=$location_id  AND `itemId`=$item_id ", true);
                                                                if ($check_sql['status'] == "success") {

                                                                ?>
                                                                    <button class="btn btn-success" type="button">Added</button>

                                                                <?php

                                                                } else {

                                                                ?>


                                                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $row['itemId'] ?>">Add</button>
                                                                <?php
                                                                }

                                                                ?>
                                                            </td>

                                                        </tr>


                                                        <!-----add form modal start --->
                                                        <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                            <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <form method="POST" action="">
                                                                            <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                                                                            <input type="hidden" name="item_id" value="<?= $row['itemId'] ?>">

                                                                            <div class="row">

                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                        <div class="card-header">

                                                                                            <h4>Storage Details</h4>

                                                                                        </div>

                                                                                        <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                    <div class="row goods-info-form-view customer-info-form-view">









                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Storage Control</label>

                                                                                                                <input type="text" name="storageControl" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Max Storage Period</label>

                                                                                                                <input type="text" name="maxStoragePeriod" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                            <div class="form-input">
                                                                                                                <label class="label-hidden" for="">Min Time Unit</label>
                                                                                                                <select id="minTime" name="minTime" class="select2 form-control">
                                                                                                                    <option value="">Min Time Unit</option>
                                                                                                                    <option value="Day">Day</option>
                                                                                                                    <option value="Month">Month</option>
                                                                                                                    <option value="Hours">Hours</option>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Minimum Remain Self life</label>

                                                                                                                <input type="text" name="minRemainSelfLife" class="form-control">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                            <div class="form-input">
                                                                                                                <label class="label-hidden" for="">Max Time Unit</label>
                                                                                                                <select id="maxTime" name="maxTime" class="select2 form-control">
                                                                                                                    <option value="">Max Time Unit</option>
                                                                                                                    <option value="Day">Day</option>
                                                                                                                    <option value="Month">Month</option>
                                                                                                                    <option value="Hours">Hours</option>

                                                                                                                </select>
                                                                                                            </div>
                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                </div>




                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                                                                        <div class="card-header">

                                                                                            <h4>Pricing and Discount

                                                                                                <span class="text-danger">*</span>

                                                                                            </h4>

                                                                                        </div>

                                                                                        <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                                                                            <div class="row">

                                                                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                                    <div class="row goods-info-form-view customer-info-form-view">

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Target price</label>

                                                                                                                <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                                                                                            <div class="form-input">

                                                                                                                <label for="">Max Discount</label>

                                                                                                                <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                                                                            </div>

                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                                                                                    </div>


                                                                                </div>





                                                                            </div>










                                                                        </form>

                                                                    </div>
                                                                    <div class="modal-body" style="height: 500px; overflow: auto;">
                                                                        <div class="card">

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!---end modal --->


                                                        <!-- right modal start here  -->

                                                        <div class="modal fade right goods-modal goods-type-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                                                <!--Content-->

                                                                <div class="modal-content">

                                                                    <!--Header-->

                                                                    <div class="modal-header pt-4">

                                                                        <div class="row item-specification-row">

                                                                            <div class="col-lg-4 col-md-4 col-sm-4">

                                                                                <div class="item-img">

                                                                                    <img src="../../public/assets/img/image/goods-item-image.png" title="goods-iem-image" alt="goods_item_image">

                                                                                </div>

                                                                            </div>

                                                                            <div class="col-lg-8 col-md-8 col-sm-8">

                                                                                <div class="head-title">

                                                                                    <p class="heading lead">Item Name : <?= $itemName ?></p>

                                                                                    <p class="item-code">Item Code : <?= $itemCode ?></p>

                                                                                    <p class="item-desc">Description : <?= $row['itemDesc'] ?></p>

                                                                                </div>

                                                                            </div>

                                                                        </div>
                                                                        <div class="display-flex-space-between mt-4 mt-3">

                                                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link active" id="home-tab<?= $row['itemId'] ?>" data-toggle="tab" href="#home<?= $row['itemId'] ?>" role="tab" aria-controls="home<?= $row['itemId'] ?>" aria-selected="true">Info</a>
                                                                                </li>

                                                                                <!-- -------------------Audit History Button Start------------------------- -->
                                                                                <li class="nav-item">
                                                                                    <a class="nav-link auditTrail" id="history-tab<?= $row['itemId'] ?>" data-toggle="tab" data-ccode="<?= $row['ItemCode'] ?>" href="#history<?= $row['itemId'] ?>" role="tab" aria-controls="history<?= $row['itemId'] ?>" aria-selected="false"><i class="fas fa-history" aria-hidden="true" style="color: #fff;"></i>Trai</la>
                                                                                </li>
                                                                                <!---------------------Audit History Button End--------------------------->
                                                                            </ul>


                                                                            <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                                                                <?php $itemId = base64_encode($row['itemId']) ?>

                                                                                <form action="" method="POST">




                                                                                    <a href="">

                                                                                        <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                                                                    </a>

                                                                                    <a href="">

                                                                                        <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                                                                    </a>

                                                                                </form>

                                                                            </div>

                                                                        </div>


                                                                    </div>



                                                                    <!--Body-->

                                                                    <div class="modal-body" style="padding: 0;">







                                                                        <div class="tab-content" id="myTabContent">



                                                                            <div class="tab-pane fade show active" id="home<?= $row['itemId'] ?>" role="tabpanel" aria-labelledby="home-tab">


                                                                                <div class="row">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                                                                        <?php if ($row['bomStatus'] != 0) { ?>

                                                                                            <a href="goods.php?bom=<?= $itemId; ?>" class="btn btn-primary float-right m-3" name="customerEditBtn">

                                                                                                <i title="BOM" class="fa fa-cogs"></i>

                                                                                                BOM

                                                                                            </a>

                                                                                        <?php } ?>

                                                                                    </div>

                                                                                </div>




                                                                                <div class="row px-3 detail-view-accordion">

                                                                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                        <!-------Classification------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                                                                        Classification
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Goods Type :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $type_name ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs"> Group :</p>
                                                                                                                    <p class="font-bold text-xs group-desc" title="Group : <?= $group_name ?>"><?= $group_name ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Availablity Check :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['availabilityCheck'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-------Basic Details------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        Basic Details
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Net Weight :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Gross Weight :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Volume :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['volume'] ?> m<sup>3</sup></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Height :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Width :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Length :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $row['length'] . "  " . $row['measuring_unit'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <?php
                                                                                        $item_id = $row['itemId'];
                                                                                        $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
                                                                                        $storage_data = $storage_sql['data'];


                                                                                        ?>

                                                                                        <!-------Storage Details------>
                                                                                        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                                                            <div class="accordion-item">
                                                                                                <h2 class="accordion-header" id="flush-headingOne">
                                                                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#storageDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                        Storage Details
                                                                                                    </button>
                                                                                                </h2>
                                                                                                <div id="storageDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                    <div class="accordion-body p-0">

                                                                                                        <div class="card">

                                                                                                            <div class="card-body p-3">

                                                                                                                <!-- <div class="display-flex-space-between">
                                                    <p class="font-bold text-xs">Storage Bin :</p>
                                                    <p class="font-bold text-xs"><?= $row['storageBin'] ?></p>
                                                  </div>

                                                  <div class="display-flex-space-between">
                                                    <p class="font-bold text-xs">Picking Area :</p>
                                                    <p class="font-bold text-xs"><?= $row['pickingArea'] ?></p>
                                                  </div>

                                                  <div class="display-flex-space-between">
                                                    <p class="font-bold text-xs">Temp Control :</p>
                                                    <p class="font-bold text-xs"><?= $row['tempControl'] ?></p>
                                                  </div> -->

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Storage Control :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['storageControl'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Max Storage Period :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriod'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Max Storage Period Time :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriodTimeUnit'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Min Remain Self Life Time Unit :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLife'] ?></p>
                                                                                                                </div>

                                                                                                                <div class="display-flex-space-between">
                                                                                                                    <p class="font-bold text-xs">Min Remain Self Life :</p>
                                                                                                                    <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLifeTimeUnit'] ?></p>
                                                                                                                </div>

                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <!-------Purchase Details------>
                                                                                        <!-- <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                        <div class="accordion-item">
                                          <h2 class="accordion-header" id="flush-headingOne">
                                            <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#purchaseDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                              Storage Details
                                            </button>
                                          </h2>
                                          <div id="purchaseDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body p-0">

                                              <div class="card">

                                                <div class="card-body p-3">

                                                  <div class="display-flex-space-between">
                                                    <p class="font-bold text-xs">Purchasing Value Key :</p>
                                                    <p class="font-bold text-xs"><?= $row['purchasingValueKey'] ?></p>
                                                  </div>



                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </div> -->

                                                                                    </div>








                                                                                </div>

                                                                            </div>

                                                                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                                                                <?= $itemName ?>

                                                                            </div>
                                                                            <!-- -------------------Audit History Tab Body Start------------------------- -->

                                                                            <div class="tab-pane fade" id="history<?= $row['itemId'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $row['itemCode'] ?>">

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





                                            </table>

                                            <?php

                                            if ($count > 0 && $count > $GLOBALS['show']) {

                                            ?>

                                                <div class="pagination align-right">

                                                    <?php pagination($count, "frm_opts"); ?>

                                                </div>



                                                <!-- End .pagination -->



                                            <?php  } ?>



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

                                    <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />

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

                                                        Base UOM</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                                                        Group</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                                                        Type</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                                                        Moving Weighted Price</td>

                                                </tr>

                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                                                        Valuation Class</td>

                                                </tr>


                                                <tr>

                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                                                        Target Price</td>

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





    $(document).ready(function() {

        $('#goodTypeDropDown')

            .select2()

            .on('select2:open', () => {

                // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodTypesFormModal">Add New</a></div>`);

            });


        function loadGLCode(accType) {
            // console.log(1);
            $.ajax({

                type: "POST",

                url: `ajaxs/accounting/ajax-getglbyp.php`,
                data: {
                    accType: accType
                },
                beforeSend: function() {

                    $("#glCode").html(`<option value="">Loding...</option>`);

                },

                success: function(response) {
                    $("#glCode").html(response);

                }

            });

        }


        $("#goodTypeDropDown").change(function() {

            let dataAttrVal = $("#goodTypeDropDown").find(':selected').data('goodtype');

            if (dataAttrVal == "RM") {

                $("#bomCheckBoxDiv").html("");

                $("#bomRadioDiv").html("");
                $("#pricing").hide();
                $("#purchase").html();
                $("#basicDetails").show();
                $("#storageDetails").show();
                $("#service_sales_details ").hide();
                $("#stockRate").show();

                $("#goodsGroup").show();
                $("#purchaseGroup").show();
                $("#availability").show();


                $("#submit_btn").show();
                $("#draft_btn").show();


            } else if (dataAttrVal == "SFG") {

                $("#bomRadioDiv").html("");

                $("#basicDetails").show();
                $("#storageDetails").show();
                $("#service_sales_details ").hide();
                $("#goodsGroup").show();
                $("#purchaseGroup").show();
                $("#availability").show();
                $("#stockRate").show();

                $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired" style="width: auto; margin-bottom: 0;" checked disabled><label class="mb-0">Required BOM</label>`);
                $("#pricing").hide();

                $("#submit_btn").show();
                $("#draft_btn").show();


            } else if (dataAttrVal == "FG") {

                $("#bomCheckBoxDiv").html(``);

                $("#purchase").html("");
                $("#basicDetails").show();
                $("#storageDetails").show();
                $("#service_sales_details ").hide();
                $("#goodsGroup").show();
                $("#purchaseGroup").show();
                $("#availability").show();
                $("#stockRate").show();

                $("#submit_btn").show();
                $("#draft_btn").show();

                $("#bomRadioDiv").html(`
        <div class="form-inline float-right" id="bomRadioDiv">

        <div class="goods-input for-manufac d-flex">

          <input type="radio" name="bomRequired_radio" value="1">

          <label for="" class="mb-0 ml-2">For Manufacturing</label>

        </div>

        <div class="goods-input for-trading d-flex">

          <input type="radio" name="bomRequired_radio" value="0">

          <label for="" class="mb-0 ml-2">For Trading</label>

        </div>

        </div>`);

                $("#pricing").show();

            } else if (dataAttrVal == "SERVICES") {

                $("#submit_btn").show();
                $("#draft_btn").show();
                $("#bomCheckBoxDiv").html(``);
                $("#purchase").hide();
                $("#bomRadioDiv").html("");
                $("#goodsGroup").show();
                $("#purchaseGroup").hide();
                $("#availability").hide();

                $("#service_sales_details ").show();
                $("#tds ").hide();

                $("#basicDetails").hide();
                $("#storageDetails").hide();
                $("#pricing").show();
                $("#stockRate").hide();
                loadGLCode(3); //INCOME GL: 3



            } else if (dataAttrVal == "SERVICEP") {

                $("#submit_btn").show();
                $("#draft_btn").show();
                $("#pricing").hide();
                $("#bomCheckBoxDiv").html(``);
                $("#purchase").hide();
                $("#bomRadioDiv").html("");
                $("#goodsGroup").show();
                $("#purchaseGroup").hide();
                $("#availability").hide();

                $("#service_sales_details ").show();
                $("#basicDetails").hide();
                $("#storageDetails").hide();
                $("#tds ").show();
                $("#stockRate").hide();

                loadGLCode(4); //EXPENSE GL:4





            } else if (dataAttrVal == "ASSET") {

                $("#submit_btn").show();
                $("#draft_btn").show();
                $("#bomCheckBoxDiv").html("");

                $("#bomRadioDiv").html("");
                $("#pricing").hide();
                $("#purchase").html();
                $("#basicDetails").show();
                $("#storageDetails").show();
                $("#service_sales_details ").hide();
                $("#stockRate").show();


                $("#goodsGroup").show();
                $("#purchaseGroup").show();
                $("#availability").show();


                loadGLCode(1); //ASSET GL:1

            } else {

                $("#submit_btn").hide();
                $("#draft_btn").hide();

                $("#bomCheckBoxDiv").html(``);
                $("#purchase").html("");
                $("#bomRadioDiv").html("");

                $("#pricing").hide();
            }

        });



        //**************************************************************

        $('#goodGroupDropDown')

            .select2()

            .on('select2:open', () => {

                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodGroupFormModal">Add New</a></div>`);

            });


        $('#buomDrop')

            .select2()

            .on('select2:open', () => {

                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewUOMFormModal">Add New</a></div>`);

            });


        $('#iuomDrop')

            .select2()

            .on('select2:open', () => {

                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewUOMFormModal">Add New</a></div>`);

            });







        $('#hsnDropDown')

            .select2()

            .on('select2:open', () => {

                $(".select2-results:not(:has(a))").append(`<div class="col-md-12 mb-12"></div>`);

            });





        $('#purchaseGroupDropDown')

            .select2()

            .on('select2:open', () => {

                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewPurchaseGroupFormModal">Add New</a></div>`);

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



<script>
    $(document).ready(function() {

        function loadGoodTypes() {

            $.ajax({

                type: "GET",

                url: `ajaxs/items/ajax-good-types.php`,

                beforeSend: function() {

                    $("#goodTypeDropDown").html(`<option value="">Loding...</option>`);

                },

                success: function(response) {

                    $("#goodTypeDropDown").html(response);



                    <?php

                    if (isset($row["goodTypeId"])) {

                    ?>

                        $(`#goodTypeDropDown option[value=<?= $row["goodTypeId"] ?>]`).attr('selected', 'selected');

                    <?php

                    }

                    ?>

                }

            });

        }

        loadGoodTypes();

        $(document).on('submit', '#addNewGoodTypesForm', function(event) {

            event.preventDefault();

            let formData = $("#addNewGoodTypesForm").serialize();

            $.ajax({

                type: "POST",

                url: `ajaxs/items/ajax-good-types.php`,

                data: formData,

                beforeSend: function() {

                    $("#addNewGoodTypesFormSubmitBtn").toggleClass("disabled");

                    $("#addNewGoodTypesFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

                },

                success: function(response) {

                    $("#goodTypeDropDown").html(response);

                    $('#addNewGoodTypesForm').trigger("reset");

                    $("#addNewGoodTypesFormModal").modal('toggle');

                    $("#addNewGoodTypesFormSubmitBtn").html("Submit");

                    $("#addNewGoodTypesFormSubmitBtn").toggleClass("disabled");

                }

            });

        });

        $(document).on("change", ".goodTypeDropDown", function() {
            let typeId = $(this).val();
            loadGoodGroup(typeId);
            load_group_modal(typeId);

        });


        function load_group_modal(typeId) {
            //console.log("hiiiiii");
            //console.log(typeId);
            $.ajax({

                type: "GET",

                url: `ajaxs/items/ajax-group-modal.php`,

                data: {
                    typeId
                },



                beforeSend: function() {

                    // $("#goodGroupDropDown").html(`<option value="">Loding...</option>`);
                    $("#goodType_input").html(``);
                    $("#goodType_id").html(``);


                },

                success: function(response) {

                    console.log(response);
                    var obj = jQuery.parseJSON(response);
                    $("#goodType_input").val(obj['type_name']);
                    $("#goodType_id").val(obj['type_id']);



                }



            });




        }

        function loadGoodGroup(typeId) {



            $.ajax({

                type: "GET",

                url: `ajaxs/items/ajax-good-groups.php`,

                data: {
                    typeId
                },



                beforeSend: function() {

                    $("#goodGroupDropDown").html(`<option value="">Loding...</option>`);

                },

                success: function(response) {

                    $("#goodGroupDropDown").html(response);

                    <?php

                    if (isset($row["goodGroupId"])) {

                    ?>


                        $(`#goodGroupDropDown option[value=<?= $row["goodGroupId"] ?>]`).attr('selected', 'selected');

                    <?php

                    }

                    ?>

                }

            });

        }

        loadGoodGroup();
        $(document).ready(function() {



            $('#addNewGoodGroupFormSubmitBtn').click(function(e) {

                //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

                event.preventDefault();

                let formData = $("#addNewGoodGroupForm").serialize();
                // console.log(formData);
                $.ajax({

                    type: "POST",

                    url: `ajaxs/items/ajax-good-groups.php`,

                    data: formData,

                    beforeSend: function() {

                        $("#addNewGoodGroupFormSubmitBtn").toggleClass("disabled");

                        $("#addNewGoodGroupFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

                    },

                    success: function(response) {
                        //console(response);
                        $("#goodGroupDropDown").html(response);

                        $('.goodGroupName').val('');
                        $('.goodGroupDesc').val('');

                        $("#addgoodGroupFormModal").modal('toggle');

                        $("#addNewgoodGroupFormSubmitBtn").html("Submit");

                        $("#addNewgoodGroupFormSubmitBtn").toggleClass("disabled");

                    }

                });

            });
        });













        function loadhsn(pageNo, limit, keyword = null) {
            $.ajax({
                method: 'POST',
                data: {
                    pageNo: pageNo,
                    limit: limit,
                    keyword: keyword,
                },
                url: `ajaxs/items/ajax-hsn.php`,
                beforeSend: function() {
                    $(".hsnSearchSpinner").show();
                    $(".hsn_tbody").html('<tr><td colspan="4"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loading ...</td></tr>');
                },
                success: function(response) {
                    $(".hsn_tbody").html(response);
                    $(".hsnSearchSpinner").hide();

                }

            });

        }

        loadhsn(0, 50);

        $(document).ready(function() {
            $(".hsnSearchSpinner").hide();
            $('#searchbar').on('keyup keydown paste', function() {
                var keyword = $(this).val();
                var pageNo = 0;
                var limit = 50;
                loadhsn(pageNo, limit, keyword);
            });
        });

        $(document).on('submit', '#addNewhsnForm', function(event) {

            event.preventDefault();

            let formData = $("#addNewhsnForm").serialize();

            $.ajax({

                type: "POST",

                url: `ajaxs/items/ajax-hsn.php`,

                data: formData,

                beforeSend: function() {

                    $("#addNewhsnFormSubmitBtn").toggleClass("disabled");

                    $("#addNewhsnFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

                },

                success: function(response) {

                    $("#hsnDropDown").html(response);

                    $('#addNewhsnForm').trigger("reset");

                    $("#addNewhsnFormModal").modal('toggle');

                    $("#addNewhsnFormSubmitBtn").html("Submit");

                    $("#addNewhsnFormSubmitBtn").toggleClass("disabled");

                }

            });

        });



        function loadPurchaseGroup() {

            $.ajax({

                type: "GET",

                url: `ajaxs/items/ajax-purchase-groups.php`,

                beforeSend: function() {

                    $("#purchaseGroupDropDown").html(`<option value="">Loding...</option>`);

                },

                success: function(response) {

                    $("#purchaseGroupDropDown").html(response);

                    <?php

                    if (isset($row["purchaseGroupId"])) {

                    ?>

                        $(`#purchaseGroupDropDown option[value=<?= $row["purchaseGroupId"] ?>]`).attr('selected', 'selected');

                    <?php

                    }

                    ?>

                }

            });

        }

        loadPurchaseGroup();


        $(document).ready(function() {

            /*@ Registration start */
            $('#addNewPurchaseGroupFormSubmitBtn').click(function(e) {


                //  $(document).on('submit', '#addNewPurchaseGroupForm', function(event) {

                event.preventDefault();

                let formData = $("#addNewPurchaseGroupForm").serialize();

                // console.log(formData);
                $.ajax({

                    type: "POST",

                    url: `ajaxs/items/ajax-purchase-groups.php`,

                    data: formData,

                    beforeSend: function() {

                        $("#addNewPurchaseGroupFormSubmitBtn").toggleClass("disabled");

                        $("#addNewPurchaseGroupFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

                    },

                    success: function(response) {

                        $("#purchaseGroupDropDown").html(response);

                        $('#addNewPurchaseGroupForm').trigger("reset");

                        $("#addNewPurchaseGroupFormModal").modal('toggle');

                        $("#addNewPurchaseGroupFormSubmitBtn").html("Submit");

                        $("#addNewPurchaseGroupFormSubmitBtn").toggleClass("disabled");

                    }

                });

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





        $(".add_data").click(function() {

            var data = this.value;

            $("#creategoodsdata").val(data);

            //confirm('Are you sure to Submit?')

            $("#goodsSubmitForm").submit();

        });





        $(".edit_data").click(function() {

            var data = this.value;

            $("#editgoodsdata").val(data);

            //confirm('Are you sure to Submit?')

            $("#goodsEditForm").submit();

        });





        //volume calculation

        function calculate_volume() {


            let height = $("#height").val();

            let width = $("#width").val();

            let length = $("#length").val();
            let vol_unit = $(".volume_unit").val();
            //console.log(vol_unit);
            if (vol_unit == "m") {


                let resm = height * length * width;

                let res = resm * 1000000;

                $("#volcm").val(res);

                $("#volm").val(resm);

            } else {

                let res = height * length * width;

                let resm = res * 0.000001;
                $("#volcm").val(res);

                $("#volm").val(resm);
            }


            //console.log(res);

            // $("#volcm").val(res);

            // $("#volm").val(resm);





        }



        // $(document).on("keyup", ".calculate_volume", function(){

        //  calculate_volume();

        // });



        $("#height").keyup(function() {

            calculate_volume();

        });

        $("#width").keyup(function() {

            calculate_volume();

        });

        $("#length").keyup(function() {

            calculate_volume();

        });






        function calculate_amount() {


            let stock = $("#stock").val();

            let rate = $("#rate").val();



            let res = stock * rate;

            $("#total").val(res);





            //console.log(res);

            // $("#volcm").val(res);

            // $("#volm").val(resm);





        }


        $("#stock").keyup(function() {

            calculate_amount();

        });

        $("#rate").keyup(function() {

            calculate_amount();

        });



        $(".volume_unit").change(function() {
            let vol_unit = $(".volume_unit").val();
            console.log(vol_unit);
            calculate_volume();

        });

        function compare() {


            let gross = $("#gross_weight").val();
            let net = $("#net_weight").val();

            if (Number(gross) < Number(net)) {
                $("#gross_span").html(`<span class="text-danger text-xs" id="gross_span">Gross weight can not Be lesser than net weight</small></span>`);



            } else {
                $("#gross_span").html("");
            }


        }

        $("#gross_weight").keyup(function() {

            compare();

        });

        $("#net_weight").keyup(function() {

            compare();

        });

        $("#gross_weight").keyup(function() {

            compare();

        });



        $("#buomDrop").change(function() {

            // let res = $(this).html();

            let res = $(this).find(":selected").text();

            $("#buom").val(res);
            $("#buom_per").html('<label id="buom_per">/' + res + '<label>')

            console.log("buomDrop", res);

        });



        $("#iuomDrop").change(function() {

            // let rel = $(this).html();

            let rel = $(this).find(":selected").text();

            $("#ioum").val(rel);

            console.log("iuomDrop", rel);

        });



        $("#goodGroupDropDown").select2({

            customClass: "Myselectbox",

        });





    });

    $('#minTime').change(function() {
        $("#maxTime option").eq($(this).find(':selected').index()).prop('selected', true);
    });
    $('#net_unit').change(function() {
        $("#gross_unit option").eq($(this).find(':selected').index()).prop('selected', true);
    });


    $(document).on("click", "#hsnsavebtn", function() {



        //console.log("clickinggggggggg");
        let radioBtnVal = $('input[name="hsn"]:checked').val();
        let hsncode = ($(`#hsnCode_${radioBtnVal}`).html());
        let hsndesc = ($(`#hsnDescription_${radioBtnVal}`).html());
        console.log(hsndesc);
        // let hsnpercentage = ($(`#taxPercentage_${radioBtnVal}`).html()).trim();
        console.log(radioBtnVal);
        $("#hsnlabel").html(radioBtnVal);
        $("#hsnlabelservice").html(radioBtnVal);
        $("#hsnDescInfo").html(hsndesc);

    });


    $(document).on("click", "#tdssavebtn", function() {



        //console.log("clickinggggggggg");
        let radioBtnVal = $('input[name="tds"]:checked').val();
        let sec = $('input[name="tds"]:checked').attr("data-attr");
        //console.log(sec);
        let section = ($(`#section_${radioBtnVal}`).html());
        // let hsndesc = ($(`#hsnDescription_${radioBtnVal}`).html()).trim();
        // let hsnpercentage = ($(`#taxPercentage_${radioBtnVal}`).html()).trim();
        console.log(radioBtnVal);
        $("#tdslabel").html(sec);

    });





    //uom add


    $('#addNewUOMFormSubmitBtn').click(function(e) {

        //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

        event.preventDefault();

        let formData = $("#addNewUOMForm").serialize();
        //console.log(formData);
        $.ajax({

            type: "POST",

            data: formData,

            url: `ajaxs/items/ajax-uom.php`,




            beforeSend: function() {

                $("#addNewUOMFormSubmitBtn").toggleClass("disabled");

                $("#addNewUOMFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

            },

            success: function(response) {
                console.log(response);
                $("#buomDrop").html(response);
                $("#iuomDrop").html(response);

                $('.UOMName').val('');
                $('.UOMDesc').val('');

                //$("#addNewUOMFormModal").modal('toggle');

                $("#addNewUOMFormSubmitBtn").html("Submit");

                $("#addNewUOMFormSubmitBtn").toggleClass("disabled");
                $('.addNewUOM').hide();


            }

        });

    });


    //end uom




    function search_hsn() {
        let input = document.getElementById('searchbar').value
        input = input.toLowerCase();
        let x = document.getElementsByClassName('hsn-code');

        for (i = 0; i < x.length; i++) {
            if (!x[i].innerHTML.toLowerCase().includes(input)) {
                x[i].style.display = "none";
            } else {
                x[i].style.display = "block";
            }
        }
    }
    $(document).on("keyup", ".form-control-sm", function() {
        var search_term = $('#searchbar').val();
        console.log(search_term)
        $('.hsn-code').removeHighlight().highlight(search_term);
    });
</script>

<script>
    $('#DataTables_Table_0').dataTable({
        "filter": true,
        "length": false
    });
</script>





<!-- <script src="<?= BASE_URL; ?>public/validations/goodsValidation.js"></script>  -->
<script src="https://johannburkard.de/resources/Johann/jquery.highlight-4.js"></script>