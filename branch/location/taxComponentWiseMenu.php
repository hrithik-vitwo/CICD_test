<?php
include("../../app/v1/connection-branch-admin.php");
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");


//console($_SESSION);


?>
 

<style>
  .reports-section .row:nth-child(2) .card {
        box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
        transition-duration: 0.2s;
        height: 100%;
        width: 230px;
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
        gap: 10px;
        padding: 0;
        justify-content: space-between;
    }

    .reports-section .row:nth-child(1) {
        margin: 15px 0;
    }

    .reports-section .row:nth-child(2) {
        margin: 5px 0 40px;
        gap: 90px;
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
        height: 80%;
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

<link rel="stylesheet" href="<?= BASE_URL?>public/assets/sales-order.css">
<link rel="stylesheet" href="<?= BASE_URL?>public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
         

            <div class="reports-section section-reports">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h4 class="text-sm font-bold border-bottom pb-2 mb-3"></h4>
                    </div>
                </div>
                <div class="row">

                  

                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-sales-orders-taxComponents.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 Sales Order
                                </div>
                                <div class="report-name">Sales Order</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-quotations-tax.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 Quotation
                                </div>
                                <div class="report-name">Quotation</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-invoices-taxComponents.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 SO Invoice
                                </div>
                                <div class="report-name">SO Invoice</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-proforma-invoice-taxComponents.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 Proforma Invoice
                                </div>
                                <div class="report-name">Proforma Invoice</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-purchases-orders-tax.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 Purchase Order
                                </div>
                                <div class="report-name">Purchase Order</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>


                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/collect-payment.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 Collection 
                                </div>
                                <div class="report-name">Collection</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>


                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-grn-new.php?posting" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 IV Posting 
                                </div>
                                <div class="report-name">IV Posting</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>

                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-debit-notes-tax.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                Debit Note
                                </div>
                                <div class="report-name"> Debit Note</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                   


                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-credit-notes-taxComponents.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 Credit Note 
                                </div>
                                <div class="report-name">Credit Note</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                   

                    <div class="col-2">
                        <a href="<?= BASE_URL ?>branch/location/manage-sales-orders-delivery-taxComponents.php" target="#">
                        <div class="card reports-card">
                            <div class="card-body">
                                <div class="icon text-center" style="color:#fff;">
                               
                                 Delivery
                                </div>
                                <div class="report-name">Delivery</p>
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>



                </div>
            </div>

            

        </div>
    </section>
</div>

<?php
include("../common/footer.php");
?>
