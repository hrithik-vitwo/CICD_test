<?php
require_once("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-migration.php");

$message = "";
if (isset($_POST["send_data"])) {

    $newSendObj = sendEmail($_POST);
    if ($newSendObj["status"] == "success") {
        swalAlert($newSendObj["status"], 'Success', $newSendObj["message"]);
    } else {
        swalAlert($newSendObj["status"], 'Warning', $newSendObj["message"]);
    }
}


?>
<style>
    .content-wrapper {
        height: 100vh !important;
    }

    .accordionGlRowHover:hover {
        background-color: #00306026 !important;
    }

    td.font-bold.bg-alter {
        background: #afc1d2;
    }

    td.bg-grey.text-white {
        background: #003060;
    }

    .blnc-sheet-card {
        background: #fff;
    }

    .blnc-sheet-card table thead tr th {
        vertical-align: middle;
    }

    .blnc-sheet-card table tbody tr td {
        background: #fff;
    }

    .blnc-sheet-card table tbody tr:nth-child(2n+1) td {
        background: #e7f2fd;
        border-color: #e7f2fd;
    }

    /* .liability-table tbody tr:nth-child(2n+1) td,
    .assets-table tbody tr:nth-child(2n+1) td {
        
    } */

    .filter-date {
        max-width: 200px;
        margin: 0 0;
        float: right;
    }
</style>
<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/accordion.css">
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>Migration</a></li>
                <!-- <li class="breadcrumb-item active">
                    <a href="manage-inventory.php?post-grn" class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add New</a>
                </li> -->
                <li class="back-button">
                    <a href="">
                        <i class="fa fa-reply po-list-icon"></i>
                    </a>
                </li>
            </ol>
        </div>
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="row p-0 m-0" style="padding: 0 200px!important;">
                        <div class="d-flex p-0 m-0 mb-4" style="justify-content: flex-end;">

                        </div>
                        <p class="text-center h6"><?= $companyNameNav ?></p>
                        <p class="text-center mt-3">Please Give Email</p>
                        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" name="add_frm" class="d-flex gap-3 col-md-4 mx-auto my-3">
                            <input type="email" name="email"  class="form-control" placeholder="Please enter email">
                            <button type="submit" value="Send" name="send_data" class="btn btn-primary" style="height: 32px;">Send</button>
                        </form>
                        <!-- <div class="grandTotal row p-0 m-0">
                            <p class="text-right text-sm pr-2">Total 20000000.00</p>
                        </div>
                         -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
include("common/footer.php");
?>