<?php
include("../../app/v1/connection-branch-admin.php");
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/company/func-branches.php");
include("../../app/v1/functions/branch/func-branch-pr-controller.php");


// console($_SESSION);


?>


<style>
    .content-wrapper {
        background: #e8eaed !important;
    }

    .reports-section .row:nth-child(2) .card {
        box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
        transition-duration: 0.2s;
        height: 10rem;
        min-height: 100%;
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
        flex-direction: column;
        justify-content: baseline !important;
        align-items: baseline;
        gap: 35px;
        padding: 0.8rem;
        background-image: url(../../public/assets/img/reports-bg.jpeg);
        background-size: contain;
        background-position: top right;
        background-repeat: no-repeat;
        border-radius: 15px;
    }


    .reports-section .row:nth-child(2) .card .card-footer button {
        float: right;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: #003060;
        color: #fff;
        border: 0;
        font-size: 12px;
    }

    .reports-section .row:nth-child(1) {
        margin: 15px 0;
    }

    .reports-section .row:nth-child(2) {
        margin: 5px 0 40px;
    }

    .reports-section .row .col {
        max-width: 250px;
    }

    .reports-section .row:nth-child(2) .card .card-body .icon img {
        width: 1.5rem;
    }

    .reports-section .row .card .card-body .icon {
        background: #003060;
        border-radius: 12px;
        width: 3rem;
        height: 3rem;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: #939393 6px 6px 16px -2px, rgba(0, 0, 0, 0.3) 6px 8px 4px -1px;
        border-radius: 50%;
    }

    .reports-section .row .card .card-body .icon ion-icon {
        color: #fff;
        font-size: 35px;
    }

    .disableReport {
        filter: grayscale(1);
    }

    @media (max-width: 768px) {
        .reports-section .row:nth-child(2) .card.reports-card .card-body {
            gap: 0px !important;
        }
    }

    @media (max-width: 425px) {
        .reports-section .row .col {
            flex: 1 1 100%;
        }
    }
</style>


<!-- <link rel="stylesheet" href="../../public/assets/manage-rfq.css">
<link rel="stylesheet" href="../../public/assets/animate.css"> -->

<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/sales-order.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="reports-section">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h4 class="text-sm font-bold border-bottom pb-2 mb-3">Correction Of Error</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-5">
                        <a href="<?= LOCATION_URL?>failed-accounting-invoices.php">
                            <div class="card reports-card">
                                <div class="card-body">
                                    <div class="icon text-center">
                                        <img width="20" src="<?= BASE_URL?>public/storage/icons/profitandloss.png" alt="">
                                    </div>
                                    <div class="report-name">
                                        <p class="font-bold text-sm mt-3">Invoice failed accounting</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button>
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col mb-5">
                        <a href="<?= LOCATION_URL?>failed-accounting-grn-srn.php">
                            <div class="card reports-card">
                                <div class="card-body">
                                    <div class="icon text-center">
                                        <img width="20" src="<?= BASE_URL?>public/storage/icons/profitandloss.png" alt="">
                                    </div>
                                    <div class="report-name">
                                        <p class="font-bold text-sm mt-3">GRN/SRN failed accounting</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button>
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col mb-5">
                        <a href="<?= LOCATION_URL?>failed-accounting-grnIv-srnIv.php">
                            <div class="card reports-card">
                                <div class="card-body">
                                    <div class="icon text-center">
                                        <img width="20" src="<?= BASE_URL?>public/storage/icons/profitandloss.png" alt="">
                                    </div>
                                    <div class="report-name">
                                        <p class="font-bold text-sm mt-3">GRN/SRN IV failed accounting</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button>
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <div class="col mb-5">
                        <a href="<?= LOCATION_URL?>failed-accounting-collectPayment.php">
                            <div class="card reports-card">
                                <div class="card-body">
                                    <div class="icon text-center">
                                        <img width="20" src="<?= BASE_URL?>public/storage/icons/profitandloss.png" alt="">
                                    </div>
                                    <div class="report-name">
                                        <p class="font-bold text-sm mt-3">Collection failed accounting</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button>
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col mb-5">
                        <a href="<?= LOCATION_URL?>failed-accounting-payment.php">
                            <div class="card reports-card">
                                <div class="card-body">
                                    <div class="icon text-center">
                                        <img width="20" src="<?= BASE_URL?>public/storage/icons/profitandloss.png" alt="">
                                    </div>
                                    <div class="report-name">
                                        <p class="font-bold text-sm mt-3">Payment failed accounting</p>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button>
                                        <i class="fa fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
</div>
</div>
</section>
</div>


<?php
include("../common/footer.php");
?>
<script>
    $(document).ready(function() {
        $(".liveToastBtn").on("click", function() {
            let Toast = Swal.mixin({
                toast: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 2000
            });
            Toast.fire({
                iconHtml: `<ion-icon name="pulse-outline"></ion-icon>`,
                title: ` Report is being reviewed !`,
                text: `This report is not available right now.`,
            });
        });
    });
</script>