<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<style>
    .mobileMenuCollapseCard {
        display: none;
    }

    .nav-box {
        display: flex;
        padding: 5px;
        background-color: #fff;
        box-shadow: 0px 0px 16px 0px #4444;
        border-radius: 8px;
        position: fixed;
        bottom: 0;
        z-index: 999;
        width: 100%;
    }

    .nav-container {
        display: flex;
        width: 100%;
        list-style: none;
        justify-content: space-around;
        padding: 0;
    }

    .nav__item {
        display: flex;
        position: relative;
        padding: 2px;
    }

    .nav__item.active .nav__item-icon {
        margin-top: -26px;
        box-shadow: 0px 0px 16px 0px #4444;
    }

    .nav__item.active .nav__item-text {
        transform: scale(1);
    }

    .nav__item-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #2f3046;
        text-decoration: none;
        z-index: 9999;
    }

    .nav__item .close {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #2f3046;
        text-decoration: none;
        z-index: 9999;
    }



    .nav__item-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6em;
        background-color: #fff;
        border-radius: 50%;
        height: 46px;
        width: 46px;
        transition: margin-top 250ms ease-in-out, box-shadow 250ms ease-in-out;
    }

    .nav__item-text {
        position: absolute;
        bottom: 0;
        transform: scale(0);
        transition: transform 250ms ease-in-out;
    }

    .menu-modal {
        backdrop-filter: blur(0px) !important;
    }

    .sc-bottom-bar {
        position: fixed;
        display: flex;
        padding: 16px 16px;
        width: 100%;
        margin: auto;
        left: 0;
        bottom: 0;
        right: 0;
        height: 62px;
        font-size: 26px;
        background-image: radial-gradient(circle at 36px 6px, transparent 36px, #ffffff 37px);
        filter: drop-shadow(0px -1px 6px rgba(0, 0, 0, 0.08)) drop-shadow(0px -2px 12px rgba(0, 0, 0, 0.12));
        border-top-left-radius: 20px;
        border-top-right-radius: 30px;
        transition: cubic-bezier(0.57, 0.23, 0.08, 0.96) 0.45s;
        bottom: 0;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .sc-nav-indicator {
        position: absolute;

        width: 56px;
        height: 56px;
        bottom: 28px;
        margin: auto;
        left: 0;

        background-color: #fff;
        box-shadow: var(--main-cast-shadow);
        border-radius: 50%;
        transition: cubic-bezier(0.45, 0.73, 0, 0.59) 0.3s;
    }

    .sc-menu-item {
        color: #003060;
        transition: ease-in-out 0.5s;
        cursor: pointer;
        display: grid;
        justify-items: center;
        gap: 5px;
    }

    .sc-menu-item i {
        color: #003060;
        font-size: 13px;
    }


    .sc-current {
        position: relative;

        color: #ffffff;

        z-index: 3;
        transform: translate3d(0px, -28px, 0px);
    }

    section.mobile-dashboard .row {
        margin: 20px 0;
    }

    section.mobile-dashboard.reports-menu .row {
        margin: 5px;
    }

    section.mobile-dashboard.location-menu .row {
        margin: 5px;
    }

    .menu-items .row {
        margin: 0 !important;
    }

    .mobile-menu-card {
        max-width: 100px;
        height: 50px;
        margin-bottom: 48px;
        margin-top: 15px;
        border-radius: 12px;
    }

    .mobile-menu-card.report,
    .mobile-menu-card.menu {
        max-width: 100%;
        height: 100px;
        margin: 0;
        border-radius: 12px;
    }

    .mobile-menu-card a {
        justify-items: center;
        gap: 5px;
        color: #000;
        padding: 10px;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        border-radius: 12px;
        height: 100%;
        display: grid;
        place-content: center;
    }

    .mobile-menu-card.menu a {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .mobile-menu-card h6 {
        text-align: center;
        font-size: 14px;
        margin: 8px 0;
    }

    section.mobile-dashboard.reports-menu .col-6 {
        padding: 10px;
    }

    section.mobile-dashboard.location-menu .col-6 {
        padding: 10px;
    }

    section.mobile-dashboard .head h4 {
        font-weight: 600;
        margin-bottom: 0;
    }

    section.mobile-dashboard hr {
        margin: 5px 0;
    }

    .menu-items {
        padding: 0;
    }

    .main-header {
        position: fixed;
        top: 0;
        width: 100%;
    }

    .content-wrapper {
        padding-top: 3em;
    }

    .menu-modal .modal-body {
        background: #2021241c;
        padding: 0;
    }

    .menu-modal .modal-body .container {
        padding: 0;
    }

    .menu-modal .modal-dialog {
        height: 93%;
        padding-top: 50px;
        max-width: 100%;
    }

    .menu-modal .modal-content {
        height: 100%;
        border-bottom: 0.5px solid #ccc;
        border-radius: 0;
        box-shadow: none;
    }

    .modal.fade:not(.in) .modal-dialog {
        animation: none !important;
    }

    footer.main-footer.text-muted {
        display: none;
    }

    @media(min-width: 768px) {
        .nav-box {
            display: none !important;
        }

        .company-logo {
            display: none;
        }
    }

    @media(max-width: 768px) {
        .nav-box {
            display: block;
        }

        li.nav-item.toggle-opener {
            display: none;
        }

        .company-logo {
            display: block;
        }

        .company-logo img {
            width: 100px;
            object-fit: cover;

        }
    }

    .sales-sub-menu {
        display: none;
    }

    .modal-backdrop.show {
        display: none;
    }
</style>

<div class="nav-box">
    <ul class="nav-container">
        <li class="nav__item active" id="mobileMenuItemDashboard">
            <a href="index.php" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="grid-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Dashboard</span>
            </a>
        </li>
        <li class="nav__item" id="nav-item">
            <a href="#account" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="wallet-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Account</span>
            </a>
        </li>
        <li class="nav__item" id="nav-item">
            <a href="manage-grn.php?post-grn" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="add-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Add</span>
            </a>
        </li>
        <li class="nav__item" id="mobileMenuItemReport">
            <a href="#report" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="bar-chart-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Report</span>
            </a>
        </li>
        <div class="modal fade menu-modal" id="reportsModal" tabindex="-1" role="dialog" data-easein="swoopIn" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog m-0">
                <div class="modal-content">

                    <div class="modal-body">
                        <div class="container" id="mobile-menu">
                            <section class="mobile-dashboard reports-menu">
                                <div class="row">
                                    <div class="col-sm-6 col-6">
                                        <div class="mobile-menu-card report">
                                            <a href="#" class="mobile-menu">
                                                <img width="30" src="../../public/storage/icons/financial-statement.png" alt="">
                                                <h6>Financial Statement</h6>
                                            </a>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="mobile-menu-card report">
                                            <a href="#" class="mobile-menu">
                                                <img width="30" src="../../public/storage/icons/expense-income.png" alt="">
                                                <h6>Expense vs Income</h6>
                                            </a>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="mobile-menu-card report">
                                            <a href="#" class="mobile-menu">
                                                <img width="30" src="../../public/storage/icons/expense-analytics.png" alt="">
                                                <h6>Expense Analytics</h6>
                                            </a>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="mobile-menu-card report">
                                            <a href="#" class="mobile-menu">
                                                <img width="30" src="../../public/storage/icons/income-analytics.png" alt="">
                                                <h6>Income Analytics</h6>
                                            </a>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="mobile-menu-card report">
                                            <a href="#" class="mobile-menu">
                                                <img width="30" src="../../public/storage/icons/money-lent-borrow.png" alt="">
                                                <h6>Money Lent/Borrowed</h6>
                                            </a>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="mobile-menu-card report">
                                            <a href="#" class="mobile-menu">
                                                <img width="30" src="../../public/storage/icons/payee.png" alt="">
                                                <h6>Payee /Payer</h6>
                                            </a>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="mobile-menu-card report">
                                            <a href="#" class="mobile-menu">
                                                <img width="30" src="../../public/storage/icons/event.png" alt="">
                                                <h6>Event</h6>
                                            </a>

                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-6">
                                        <div class="mobile-menu-card report">
                                            <a href="#" class="mobile-menu">
                                                <img width="30" src="../../public/storage/icons/finanacial-analysis.png" alt="">
                                                <h6>Financial Analysis</h6>
                                            </a>

                                        </div>
                                    </div>

                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <li class="nav__item" id="mobileMenuItemMenu">
            <a href="#menu" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="apps-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Menu</span>
            </a>

        </li>

        <div class="modal fade menu-modal" id="menuModal" tabindex="-1" role="dialog" data-easein="swoopIn" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog m-0" role="document">
                <div class="modal-content">

                    <div class="modal-body">
                        <div class="container-fluid" id="mobile-menu">
                            <section class="mobile-dashboard location-menu">

                                <div class="row">

                                    <div class="col-md-4 col-sm-4 col-6">
                                        <div class="mobile-menu-card menu">
                                            <a class="mobile-menu mobileMenuCollapseBtn" id="mobileMenuCollapseBtn_1">
                                                <div class="img-title">
                                                    <img width="30" src="../../public/storage/icons/sales.png" alt="sales-sub-menu">
                                                    <h6>Sales</h6>
                                                </div>
                                                <ion-icon class="right-arrow" id="right-arrow-ion" name="chevron-forward-outline" size="small"></ion-icon>
                                            </a>


                                        </div>
                                    </div>

                                    <div class="row mobileMenuCollapseCard" id="mobileMenuCollapseCard_1">
                                        <div class="col-lg-12 col-md-12 col-sm-12 menu-items">
                                            <div class="row">
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6>Customer Master</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-sales-orders-delivery.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6> Sales Order</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-pgi.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/so-delivery.png" alt="icons"> </a>
                                                        <h6>Sales Order Delivery</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-invoices.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/PGI.png" alt="icons"> </a>
                                                        <h6>PGI</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-revenues.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/invoice.png" alt="icons"> </a>
                                                        <h6>Invoice</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-credit-notes.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/google-sheets.png" alt="icons"> </a>
                                                        <h6>FG Stock</h6>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-4 col-6">
                                        <div class="mobile-menu-card menu">
                                            <a href="#" class="mobile-menu mobileMenuCollapseBtn" id="mobileMenuCollapseBtn_2">
                                                <div class="img-title">
                                                    <img width="30" src="../../public/storage/icons/expense-income.png" alt="">
                                                    <h6>Material</h6>
                                                </div>
                                                <ion-icon class="right-arrow" id="right-arrow-ion" name="chevron-forward-outline" size="small"></ion-icon>
                                            </a>

                                        </div>
                                    </div>

                                    <div class="row mobileMenuCollapseCard" id="mobileMenuCollapseCard_2">
                                        <div class="col-lg-12 col-md-12 col-sm-12 menu-items">
                                            <div class="row">
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6>Item Master</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-sales-orders-delivery.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6> Inventory</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-pgi.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/so-delivery.png" alt="icons"> </a>
                                                        <h6>Transfer Posting</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-invoices.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/PGI.png" alt="icons"> </a>
                                                        <h6>GRN</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-revenues.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/invoice.png" alt="icons"> </a>
                                                        <h6>Vendor</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-credit-notes.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/google-sheets.png" alt="icons"> </a>
                                                        <h6>PR</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-credit-notes.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/google-sheets.png" alt="icons"> </a>
                                                        <h6>RFQ</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-credit-notes.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/google-sheets.png" alt="icons"> </a>
                                                        <h6>Quotations</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-credit-notes.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/google-sheets.png" alt="icons"> </a>
                                                        <h6>PO</h6>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>






                                    <div class="col-md-4 col-sm-4 col-6">
                                        <div class="mobile-menu-card menu">
                                            <a href="#" class="mobile-menu mobileMenuCollapseBtn" id="mobileMenuCollapseBtn_3">
                                                <div class="img-title">
                                                    <img width="30" src="../../public/storage/icons/expense-analytics.png" alt="">
                                                    <h6>Production</h6>
                                                </div>
                                                <ion-icon class="right-arrow" id="right-arrow-ion" name="chevron-forward-outline" size="small"></ion-icon>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="row mobileMenuCollapseCard" id="mobileMenuCollapseCard_3">
                                        <div class="col-lg-12 col-md-12 col-sm-12 menu-items">
                                            <div class="row">
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6>BOM</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-sales-orders-delivery.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6>Production Order</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-pgi.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/so-delivery.png" alt="icons"> </a>
                                                        <h6>MRP</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 col-3">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-invoices.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/PGI.png" alt="icons"> </a>
                                                        <h6>Consumption Posting</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-4 col-6">
                                        <div class="mobile-menu-card menu">
                                            <a href="#" class="mobile-menu mobileMenuCollapseBtn" id="mobileMenuCollapseBtn_4">
                                                <div class="img-title">
                                                    <img width="30" src="../../public/storage/icons/income-analytics.png" alt="">
                                                    <h6>Warehousing</h6>
                                                </div>
                                                <ion-icon class="right-arrow" id="right-arrow-ion" name="chevron-forward-outline" size="small"></ion-icon>
                                            </a>

                                        </div>
                                    </div>

                                    <div class="row  mobileMenuCollapseCard" id="mobileMenuCollapseCard_4">
                                        <div class="col-lg-12 col-md-12 col-sm-12 menu-items">
                                            <div class="row">
                                                <div class="col-sm-4 col-4">
                                                    <div class="mobile-menu-card">
                                                        <a class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6>Warehouse Master</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-4">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-sales-orders-delivery.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6>Storage Location</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 col-4">
                                                    <div class="mobile-menu-card">
                                                        <a href="manage-pgi.php" class="mobile-menu">
                                                            <img width="20" src="../../public/storage/icons/so-delivery.png" alt="icons"> </a>
                                                        <h6>BIN</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>





                                    <div class="col-md-4 col-sm-4 col-6">
                                        <div class="mobile-menu-card menu">
                                            <a href="#" class="mobile-menu mobileMenuCollapseBtn" id="mobileMenuCollapseBtn_5">
                                                <div class="img-title">
                                                    <img width="30" src="../../public/storage/icons/money-lent-borrow.png" alt="">
                                                    <h6>Finance</h6>
                                                </div>
                                                <ion-icon class="right-arrow" id="right-arrow-ion" name="chevron-forward-outline" size="small"></ion-icon>
                                            </a>

                                        </div>
                                    </div>

                                    <div class="row mobileMenuCollapseCard" id="mobileMenuCollapseCard_5">

                                        <div class="col-lg-12 col-md-12 col-sm-12 menu-items">
                                            <div class="row">
                                                <div class="col-sm-6 col-6">
                                                    <div class="mobile-menu-card" style="max-width: 100%;">
                                                        <a href="#" class="mobile-menu mobileMenuCollapseBtn" id="mobileMenuCollapseBtn_6">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6>Receivables</h6>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-6">
                                                    <div class="mobile-menu-card" style="max-width: 100%;">
                                                        <a href="#" class="mobile-menu mobileMenuCollapseBtn" id="mobileMenuCollapseBtn_7">
                                                            <img width="20" src="../../public/storage/icons/sales-order.png" alt="icons"> </a>
                                                        <h6>Payables</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-sm-4 col-6">
                                        <div class="mobile-menu-card menu">
                                            <a href="#" class="mobile-menu">
                                                <div class="img-title">
                                                    <img width="30" src="../../public/storage/icons/payee.png" alt="">
                                                    <h6>Reports</h6>
                                                </div>
                                                <ion-icon class="right-arrow" id="right-arrow-ion" name="chevron-forward-outline" size="small"></ion-icon>

                                            </a>
                                        </div>
                                    </div>


                                </div>
                            </section>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </ul>

</div>



<script>
    const list = document.querySelectorAll(".nav__item");
    list.forEach((item) => {
        item.addEventListener("click", () => {
            list.forEach((item) => item.classList.remove("active"));
            item.classList.add("active");
        });
    });

    $(document).ready(function() {
        $(document).on("click", "#mobileMenuItemDashboard", function() {
            $("#reportsModal").modal("hide");
            $("#menuModal").modal("hide");
            //$("#mobileMenuItemMenu").modal("hide");
            //$("#reportsModal").modal("toggle");
        });
        $(document).on("click", "#mobileMenuItemReport", function() {
            $("#reportsModal").modal("toggle");
            $("#menuModal").modal("hide");
            console.log("hello world");
            //$("#mobileMenuItemMenu").modal("hide");
            //$("#reportsModal").modal("toggle");
        });
        $(document).on("click", "#mobileMenuItemMenu", function() {
            $("#menuModal").modal("toggle");
            $("#reportsModal").modal("hide");
            // $("#reportsModal").modal("hide");
            //$("#menuModal").modal("toggle");
        });
    });
</script>

<script>
    $(document).ready(function() {
        let prevSelectedMenu = null;
        $(document).on("click", ".mobileMenuCollapseBtn", function() {
            let menuId = ($(this).attr("id")).split("_")[1];
            if (prevSelectedMenu == menuId) {
                $(`#mobileMenuCollapseCard_${menuId}`).toggle(300);
            } else {
                $(`.mobileMenuCollapseCard`).hide(300);
                $(`#mobileMenuCollapseCard_${menuId}`).toggle(300);
            }
            prevSelectedMenu = menuId;
        });
    });


    // $('.Show').click(function() {
    //     $('#target').show(300);
    //     $('.Show').hide(100);
    //     $('.Hide').show(50);
    // });
    // $('.Hide').click(function() {
    //     $('#target').hide(300);
    //     $('.Show').show(100);
    //     $('.Hide').hide(50);
    // });
    // $('#toggle').click(function() {
    //     $('#target').slideToggle("slow", function() {});
    // });

    // $('.Show').click(function() {
    //     $('#target-1').show(300);
    //     $('.Show').hide(100);
    //     $('.Hide').show(50);
    // });
    // $('.Hide').click(function() {
    //     $('#target-1').hide(300);
    //     $('.Show').show(100);
    //     $('.Hide').hide(50);
    // });
    // $('#toggle-1').click(function() {
    //     $('#target-1').slideToggle("slow", function() {});
    // });

    // $("#right-arrow-ion").click(function() {
    //     $(this).toggleClass("rotate");
    // })
</script>
<script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>


<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>