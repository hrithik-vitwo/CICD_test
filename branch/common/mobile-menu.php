<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/listing.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/mobile-menu.css">
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
            <a href="manage-grn-invoice.php" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="add-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Add</span>
            </a>
        </li>
        <li class="nav__item" id="mobileMenuItemReport">
            <a href="<?= BASE_URL ?>branch/location/manage-reports.php?pmKey=OTU" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="bar-chart-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Report</span>
            </a>
        </li>
        <!-- <div class="modal fade zoom-in menu-modal" id="reportsModal" tabindex="-1" role="dialog" data-easein="swoopIn" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
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
        </div> -->
        <li class="nav__item" id="mobileMenuItemMenu">
            <a href="#menu" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="apps-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Menu</span>
            </a>
        </li>
        <div class="modal fade zoom-in menu-modal" id="menuModal" tabindex="-1" role="dialog" data-easein="swoopIn" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog m-0" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="container-fluid" id="mobile-menu">
                            <section class="mobile-dashboard location-menu">
                                <div class="row">
                                    <?php
                                    // console($menuSubMenuListObj);
                                    foreach ($menuSubMenuListObj as $key => $grandmenu) {
                                        if (isset($grandmenu['subParentMenus']) && !empty($grandmenu['subParentMenus'])) {
                                            foreach ($grandmenu['subParentMenus'] as $key2 => $oneMenu) {
                                    ?>
                                                <div class="col-md-4 col-sm-4 col-6">
                                                    <div class="mobile-menu-card menu">
                                                        <a class="mobile-menu mobileMenuCollapseBtn" id="mobileMenuCollapseBtn_<?= $key2; ?>">
                                                            <div class="img-title">
                                                                <?= $oneMenu["menuIcon"] ?>
                                                                <h6><?= $oneMenu['menuLabel']; ?></h6>
                                                            </div>
                                                            <ion-icon class="right-arrow" id="right-arrow-ion" name="chevron-forward-outline" size="small"></ion-icon>
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="row mobileMenuCollapseCard" id="mobileMenuCollapseCard_<?= $key2; ?>">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 menu-items">
                                                        <div class="row">
                                                            <?php
                                                            foreach ($oneMenu["subMenus"] as $oneSubMenu) {
                                                            ?>
                                                                <div class="col-sm-3 col-3">
                                                                    <div class="mobile-menu-card">
                                                                        <a class="mobile-menu" href="<?= $oneSubMenu["menuFile"] ?>">
                                                                            <?= $oneSubMenu["menuIcon"] ?> </a>
                                                                        <h6><?= $oneSubMenu["menuLabel"] ?></h6>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
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
                $(`#mobileMenuCollapseCard_${menuId}`).slideToggle(200);
            } else {
                $(`.mobileMenuCollapseCard`).hide(200);
                $(`#mobileMenuCollapseCard_${menuId}`).slideToggle(200);
            }
            prevSelectedMenu = menuId;
        });
    });
</script>
<script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>