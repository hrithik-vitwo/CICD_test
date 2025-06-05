<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");


// console($_SESSION);


?>


<style>
   .content-wrapper {
        background: #e8eaed !important;
        height: auto !important;
    }

    .reports-section .row:nth-child(2) .card {
        box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
        transition-duration: 0.2s;
        height: 80%;
        background: #fff;
    }

    .reports-section .row:nth-child(2) a {
        text-decoration: none;
        color: #000;
    }

    .reports-section .row:nth-child(2) .card:hover {
        box-shadow: rgba(50, 50, 93, 0.25) 2px 8px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
    }

    .reports-section .row:nth-child(2) .card .card-body {
        display: flex;
        align-items: center;
        padding: 0;
        justify-content: space-around;
    }

    .reports-section .row:nth-child(1) {
        margin: 15px 0;
    }

    .reports-section .row:nth-child(2) {
        margin: 40px 0 40px;
    }

    .reports-section .row .col {
        max-width: 250px;
    }

    .reports-section .row:nth-child(2) .card .card-body .icon img {
        width: 40px;
    }

    .reports-section .row .card .card-body .icon {
        background: #003060;
        border-radius: 12px;
        height: 70px;
        width: 70px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        top: -30px;
        left: 10px;
        box-shadow: #939393 6px 6px 16px -2px, rgba(0, 0, 0, 0.3) 6px 8px 4px -1px;
    }

    ion-icon {
        color: #fff;
        font-size: 35px;
    }
</style>


<!-- <link rel="stylesheet" href="../public/assets/manage-rfq.css">
<link rel="stylesheet" href="../public/assets/animate.css"> -->

<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/sales-order.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <?php
            $pmKey = base64_decode($_GET["pmKey"]) ?? '139';
            $searchMM = array("../../", "../");
            foreach ($menuSubMenuListObj['data'][$pmKey]['subParentMenus'] as $key2 => $oneMenu) {
                // console($oneMenu);

            ?>
                <div class="reports-section">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <h4 class="text-sm font-bold border-bottom pb-2 mb-3"><?= $oneMenu['menuLabel']; ?></h4>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        foreach ($oneMenu["subMenus"] as $oneSubMenu) {
                        ?>
                            <div class="col">
                                <a href="<?= $oneSubMenu['extraPrefixFolder']; ?><?= $oneSubMenu['menuFile']; ?>">
                                    <div class="card reports-card">
                                        <div class="card-body">
                                            <div class="icon text-center">
                                                <?= str_replace($searchMM, BASE_URL, $oneSubMenu['menuIcon']); ?>
                                            </div>
                                            <div class="report-name">
                                                <p class="font-bold text-xs text-center"><?= $oneSubMenu['menuLabel']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            <?php } ?>

        </div>
</div>
</div>
</section>
</div>

<?php
include("common/footer.php");
?>
<script>

</script>