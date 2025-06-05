<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/pagination.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/export.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

$so_controller = new BranchSo();



if (isset($_POST["add-table-settings"])) {

    // console($_POST);
    // exit();

    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    // console($editDataObj);
    // exit();
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩  
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩  





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



    .bar-code-title.d-flex {
        font-family: cursive;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 600;
    }

    .bar-code-title.d-flex p {
        font-family: cursive;
        color: #fff;
    }

    .card.bar-code-multiple-card {
        border-radius: 5px;
        padding: 5px;
        max-width: 200px;
        width: 100%;
        box-shadow: 6px 7px 12px -3px #00000052;
    }

    .card.bar-code-multiple-card .card-footer {
        background-color: #003060;
    }

    svg.bar-code-img {
        max-width: 300px;
        width: 100%;
        height: auto;
        display: block;
    }

    .row.bar-code-cards {
        gap: 20px;
    }

    .bar-code-btns {
        gap: 7px;
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


                        <div class="filter-list">
                            <a href="#" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2 active"></i>Print All Bar Code</a>
                        </div>

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

                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                            
                                           
                                        <div class="card" style="border-radius: 20px;">
                                    <div class="row bar-code-cards p-0 m-0">
                                        <?php

                                        $qty = $_GET["quantity"];
                                        $batch = $_GET["batchNumber"];
                                        $born = $_GET["bornDate"];

                                        for ($i = 1; $i <= $qty; $i++) {
                                        ?>
                                            <div class="card bar-code-multiple-card m-2">
                                                <div class="card-body p-0">
                                                    <svg class="bar-code-img" id="barcode<?= $i ?>"></svg>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="bar-code-title d-flex">
                                                        <p>Born Date</p>
                                                        <p><?= $born ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    JsBarcode("#barcode<?= $i ?>", "<?= $batch ?>", {
                                                        fontSize: 14,
                                                        fontOptions: "bold",
                                                        margin: 5,
                                                        height: 75,
                                                        width: 1
                                                    });
                                                });
                                            </script>
                                        <?php
                                        }
                                        ?>
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
