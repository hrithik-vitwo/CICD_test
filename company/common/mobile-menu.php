<link rel="stylesheet" href="../public/assets/ref-style.css">
<link rel="stylesheet" href="../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
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

    @media (min-width: 769px) {
        .nav-box {
            display: none;
        }
    }
</style>

<div class="nav-box">
    <ul class="nav-container">
        <li class="nav__item active" id="nav-item">
            <a href="#dashboard" class="nav__item-link">
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
            <a href="#add" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="add-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Add</span>
            </a>
        </li>
        <li class="nav__item" id="nav-item">
            <a href="#report" class="nav__item-link">
                <div class="nav__item-icon">
                    <ion-icon name="bar-chart-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Report</span>
            </a>
        </li>
        <li class="nav__item">
            <a href="#menu" class="nav__item-link" id="nav-item" data-toggle="modal" data-target="#menuModal">
                <div class="nav__item-icon">
                    <ion-icon name="apps-outline"></ion-icon>
                </div>
                <span class="nav__item-text">Menu</span>
            </a>
            <div class="modal fade menu-modal" id="menuModal" tabindex="-1" role="dialog" data-easein="swoopIn" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog m-0" role="document">
                    <div class="modal-content">

                        <div class="modal-body">
                            <div class="container" id="mobile-menu">
                                <section class="mobile-dashboard">
                                    <?php
                                    $menuSubMenuListObj = getAdministratorMenuSubMenu();
                                    // console($menuSubMenuListObj);
                                    if ($menuSubMenuListObj["status"] == "success") {

                                        foreach ($menuSubMenuListObj["data"] as $oneMenu) {
                                    ?>
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="head">
                                                        <h4><?= $oneMenu["menuLabel"] ?></h4>
                                                    </div>
                                                    <hr>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 menu-items">
                                                    <div class="row">
                                                        <?php
                                                        foreach ($oneMenu["subMenus"] as $oneSubMenu) {
                                                        ?>
                                                            <div class="col-sm-3 col-3">
                                                                <div class="mobile-menu-card">
                                                                    <a href="<?= $oneSubMenu["menuFile"] ?>" class="mobile-menu">
                                                                        <?= $oneSubMenu["menuIcon"] ?>
                                                                    </a>
                                                                    <h6> <?= $oneSubMenu["menuLabel"] ?></h6>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php }
                                    }

                                    if ($_SESSION["logedCompanyAdminInfo"]["adminRole"] == 1) { ?>

                                        <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="head">
                                                    <h4>Administrator</h4>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12 menu-items">
                                                <div class="row">
                                                    <div class="col-sm-3 col-3">
                                                        <div class="mobile-menu-card">
                                                            <a href="<?= COMPANY_URL ?>administrator-role.php" class="mobile-menu">
                                                                <i class="fa fa-info"></i>
                                                            </a>
                                                            <h6>Manage Roles</h6>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-3">
                                                        <div class="mobile-menu-card">
                                                            <a href="<?= COMPANY_URL ?>administrator-user.php" class="mobile-menu">
                                                                <i class="fa fa-info"></i>
                                                            </a>
                                                            <h6>Manage Admin Users</h6>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-3">
                                                        <div class="mobile-menu-card">
                                                            <a href="<?= COMPANY_URL ?>administrator-setting.php" class="mobile-menu">
                                                                <i class="fa fa-info"></i>
                                                            </a>
                                                            <h6>Manage Settings</h6>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>

                                </section>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </li>

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
</script>
<script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>