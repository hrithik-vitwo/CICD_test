<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/pagination.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-stock-post.php");

if (isset($_POST["add-table-settings"])) {

    // console($_POST);
    // exit();

    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    // console($editDataObj);
    // exit();
    swalToast($editDataObj["status"], $editDataObj["message"],'manage-physical-stock.php');
}
if(isset($_GET) && $_GET['type']== "post"){
    $id = $_GET['post'];
    $post = post_stock($id);

    swalToast($post["status"], $post["message"],"manage-physical-stock.php"); 
   
}

if(isset($_GET) && $_GET['type']== "delete"){
    $id = $_GET['id'];
    $post = delete_stock($id);

    swalToast($post["status"], $post["message"],"manage-physical-stock.php"); 
   
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

    .rfq-modal .tab-content li a span,
    .rfq-modal .tab-content li a i {

        font-weight: 600 !important;

    }


    .float-add-btn {

        display: flex !important;

    }

    .items-search-btn {

        display: flex;

        align-items: center;

        gap: 5px;

        border: 1px solid #fff !important;

    }

    .card.existing-vendor .card-header,
    .card.other-vendor .card-header {

        display: flex;

        justify-content: space-between;

    }

    .card.existing-vendor a.btn-primary,
    .card.other-vendor a.btn-primary {

        padding: 3px 12px;

        margin-right: 10px;

        float: right;

        border: 1px solid #fff !important;

    }



    .card-body::after,
    .card-footer::after,
    .card-header::after {

        display: none;

    }

    .row.rfq-vendor-list-row-value {

        border-bottom: 1px solid #fff;

        margin: 0;

        align-items: center;

    }

    .row.rfq-vendor-list-row {

        margin: 0;

        border-bottom: 1px solid #fff;

        align-items: center;

    }

    .rfq-email-filter-modal .modal-dialog {

        max-width: 650px;

    }

    .date-range-input {
        gap: 13px;
        justify-content: flex-end;
    }

    .row.custom-range-row {
        align-items: center;
    }

    .goods-flex-btn form {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .customrange-section {
        position: absolute;
        bottom: 20px;
        right: 270px;
    }

    .vendor-gstin {
        margin: 90px auto;
    }

    .display-none {
        display: none;
    }

    .stock-action-bts {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding-right: 1em;
    }

    input.btn.btn-primary {
        background-color: #003060 !important;
        border-color: #003060 !important;
        margin: 20px 0px 0px;
        float: right;
    }
     .modal.manage-physical-stock .nav.nav-tabs li.nav-item a {
        background-color: #003060;
    }
     .modal.manage-physical-stock .nav.nav-tabs li.nav-item a:hover {
        background-color: #003060 !important;
        color: #fff !important;
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


<style>
    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border: 1px solid #ccc;
        z-index: 9999;
    }
</style>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        /* background-color: rgba(0, 0, 0, 0.4); */
    }

    .add-stock-list-modal .modal-dialog {
        width: 100%;
        max-width: 70%;
    }



    .add-stock-list-modal .modal-content {
        background-color: #fefefe;
        padding: 20px;
        border: 1px solid #888;
        margin: 0 auto;
        height: 500px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>



    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">


                      






                        <div class="card card-tabs" style="border-radius: 20px;">

                            <div class="card-body">
                                <div class="row filter-serach-row">


                                    <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="row custom-range-row">
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position: absolute; z-index: 999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                            </div>

                                            <div class="col-lg-10 col-md-10 col-sm-12">
                                                <div class="section serach-input-section">
                                                    <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                    <div class="icons-container">
                                                        <div class="icon-search">
                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
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
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Filter
                                                        Payroll</h5>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                                                    echo $_REQUEST['keyword'];
                                                                                                                                                                                } ?>">
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <select id="pr" name="pr" class="fld form-control m-input">
                                                                <option value="">ALL</option>
                                                                <?php

                                                                $pr_query = "SELECT * FROM erp_branch_purchase_request WHERE company_id = '$company_id' AND branch_id = '$branch_id' AND location_id = '$location_id'";
                                                                $pr_query_list = queryGet($pr_query, true);
                                                                $pr_list = $pr_query_list['data'];
                                                                foreach ($pr_list as $pr_row) {
                                                                ?>
                                                                    <option value="<?= $pr_row['purchaseRequestId'] ?>" <?php if (isset($_GET['prid']) && $_GET['prid'] == $pr_row['purchaseRequestId']) echo ("selected"); ?>><?= $pr_row['prCode'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
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
                                                    <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync fa-spin"></i>Reset</a>
                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                        Search</button>
                                                </div>



                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                            <?php
                                            $cond = '';
                                            global $company_id;
                                            global $branch_id;
                                            global $location_id;






                                            $batch = queryGet("SELECT * FROM `erp_stock_count_parent` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id", true);

                                           // console($batch);





                                            // $num_list = $batch['numRows'];

                                            $count = $batch['numRows'];
                                            $cnt = $GLOBALS['start'] + 1;
                                            // exit();
                                            $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_STOCK_COUNT", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                            $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                            $settingsCheckbox = unserialize($settingsCh);
                                            $settingsCheckboxCount = count($settingsCheckbox);
                                            ?>
                                           





                                            <table class="table  table-hover text-nowrap" id="export_item">
                                                <thead>
                                                    <tr class="alert-light">
                                                        <th>#</th>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <th>ID</th>

                                                        <?php }

                                                        if (in_array(2, $settingsCheckbox)) { ?>

                                                            <th> From Date </th>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>

                                                            <th>To Date</th>
                                                        <?php }
                                                         if (in_array(4, $settingsCheckbox)) { ?>

                                                            <th>Status</th>
                                                        <?php }
                                                       
                                                        

                                                        ?>
                                                        <th >View</th>
                                                        
                                                    </tr>
                                                </thead>



                                                <tbody>
                                                    <?php
                                                    //$user_arr = [];
                                                    // console($BranchPrObj->fetchBranchSoListing()['data']);

                                                    foreach ($batch['data'] as $data) {
                                                        //console($data);
                                                    ?>


                                                        <tr style="cursor:pointer">
                                                            <td><?= $cnt++ ?></td>
                                                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                                <td><?= $data['count_id']  ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(2, $settingsCheckbox)) { ?>
                                                                <td><?= $data['from_date'] ?>

                                                                </td>

                                                            <?php }
                                                            if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $data['to_date'] ?>

                                                                </td>

                                                            <?php }
                                                              if (in_array(3, $settingsCheckbox)) { ?>
                                                                <td><?= $data['status'] ?>

                                                                </td>

                                                            <?php }

                                                           ?>

<td>
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $data['count_id'] ?>" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                                        </td>

                                                        </tr>


                                                        <!-- right modal start here  -->

                                                    <div class="modal fade right customer-modal manage-physical-stock" id="fluidModalRightSuccessDemo_<?= $data['count_id'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                            <!--Content-->
                                                            <div class="modal-content">
                                                                <!--Header-->
                                                                <div class="modal-header">

                                                                    <div class="customer-head-info">
                                                                        <div class="customer-name-code">
                                                                           
                                                                            <p class="heading lead"><?= $data['count_id'] ?></p>
                                                                         
                                                                        </div>
                                                                        
                                                                    </div>

                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home<?= $data['count_id'] ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>
                                                                        </li>
                                                                        
                                                                       
                                                                        <!-- -------------------Audit History Button End------------------------- -->
                                                                    </ul>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="tab-content" id="myTabContent">
                                                                        <div class="tab-pane fade show active" id="home<?= $data['count_id'] ?>" role="tabpanel" aria-labelledby="home-tab">

                                                                        <?php
                                                                        
                                                                       if($data['status'] == 'active' ){
                                                                        ?>

                                                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                        
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="" href="manage-physical-stock.php?type=delete&id=<?=$data['count_id']  ?>" role="" aria-controls="profile" aria-selected="false">Post</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" id="" href="pending-po.php?delete=<?=$data['count_id']  ?>" role="" aria-controls="profile" aria-selected="false">Delete</a>
                                                                        </li>
                                                                        </ul>
                                                                        <?php
                                                                       }
                                                                       ?>
                                                                                <!-- <form action="" method="POST">

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

                                                                                </form> -->

                                                                                <div class="item-detail-section">
                                                                                    <h6>Items Details</h6>

                                                                                <?php
                                                                                $parent_id = $data['count_id'];
                                                                                $batch_detail = queryGet("SELECT * FROM `erp_stock_count` as count LEFT JOIN `erp_inventory_items` as items ON items.itemId = count.itemId WHERE `parent_id` = $parent_id",true);
                                                                                   // console($batch_detail);
                                                                                foreach($batch_detail['data'] as $oneItem){

                                                                                ?>


                                                                               

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
                                                                                                            <p><?= $oneItem['itemQty'] ?></p>
                                                                                                            <p>
                                                                                                                <h10>Quantity- <?= $oneItem['itemQty'] ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Count-  <?= $oneItem['stockCount'] ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Difference-  <?= $oneItem['stockDifference'] ?></h10>
                                                                                                            </p>
                                                                                                            <p>
                                                                                                                <h10>Batch Number-  <?= $oneItem['batchNumber'] ?></h10>
                                                                                                            </p>
                                                                                                          

                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <hr>
                                                                                           
                                                                                           
                                                                                        </div>
                                                                                    </div>

                                                                               

                                                                                <?php

                                                                                }

                                                                                ?>
                                                                           
                                                                           </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                            



                                                    <?php

                                                    }
                                                    ?>
                                                </tbody>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="<?= $settingsCheckboxCount + 2; ?>">
                                                            <!-- Start .pagination -->

                                                            <?php
                                                            if ($count > 0 && $count > $GLOBALS['show']) {
                                                            ?>
                                                                <div class="pagination align-right">
                                                                    <?php pagination($count, "frm_opts"); ?>
                                                                </div>

                                                                <!-- End .pagination -->

                                                            <?php } ?>

                                                            <!-- End .pagination -->
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <!-- modal -->
                                            <div class="modal fade" id="addnewFile">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content card bg-white p-0">
                                                        <div class="modal-header card-header p-3">
                                                            <h4 class="modal-title" id="exampleModalLabel">Import Excel File</h4>
                                                        </div>
                                                        <div class="modal-body card-body p-3">
                                                            <form id="uploadForm" enctype="multipart/form-data">
                                                                <input class="form-control" type="file" id="excelFile" name="excelFile" accept=".xls, .xlsx">
                                                                <input class="btn btn-primary" type="submit" value="Preview">
                                                            </form>

                                                            <div id="previewModal" class="modal add-stock-list-modal">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h3 class="card-title">Excel Preview</h3>
                                                                            <span class="close">&times;</span>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div id="excelData">

                                                                            </div>
                                                                        </div>
                                                                        <!-- <div class="modal-footer">
                                                                            <button class="btn btn-primary" id="insertButton">Insert into Database</button>
                                                                        </div> -->
                                                                    </div>
                                                                </div>
                                                            </div>
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
                                                            <input type="hidden" name="pageTableName" value="ERP_STOCK_COUNT" />
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
                                                                                Batch Number</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                                                Item Name</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                                                Storage Type</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                                                Item Quantity </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                                                Item UOM</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                                                Item Price</td>
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

    <form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
        <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                        echo  $_REQUEST['pageNo'];
                                                    } ?>">
    </form>

<?php

require_once("../common/footer.php");





?>