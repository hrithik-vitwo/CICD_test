<?php

use Rakit\Validation\Rules\Uppercase;
?>

<style>
  body.sidebar-mini.layout-fixed nav.main-header.navbar.navbar-expand.navbar-white.navbar-light {
    position: fixed;
    width: 100%;
    justify-content: space-between;
    padding-left: 0px;
    padding-right: 0px;
  }

  body.sidebar-mini.layout-fixed.sidebar-collapse .navbar-nav-user-dropdown {
    right: 97px;
    position: relative;
  }

  body.sidebar-mini.layout-fixed .navbar-nav-user-dropdown {
    right: 275px;
    position: relative;
  }

  .content-wrapper {
    padding-top: 70px;
  }

  ul.navbar-nav-user-dropdown .breadcrumb-item+.breadcrumb-item::before {
    float: left;
    padding-right: 0.5rem;
    color: #6c757d;
    content: ">";
    margin-left: 1em;
  }

  ul.navbar-nav-user-dropdown .breadcrumb-item img {
    width: 30px;
    height: 50px;
    object-fit: contain;
  }


  a.dropdown-toggle p {
    color: #212529;
  }

  .navbar-expand .navbar-nav .dropdown-menu {
    top: 42px;
    position: absolute;
  }

  a.dropdown-item {
    justify-content: center;
  }

  div.waves-ripple {
    display: none !important;
    top: 0;
  }

  

  @media (max-width: 768px) {

    .dropdown {
      padding: 3px;
      border-radius: 10px;
      box-shadow: 0px 3px 12px -3px #b9b9b9;
    }

    body.sidebar-mini.layout-fixed nav.main-header.navbar.navbar-expand.navbar-white.navbar-light {
      height: 60px;
      margin-left: 0;
    }

    body.sidebar-mini.layout-fixed .navbar-nav-user-dropdown {
      right: 25px !important;
      position: relative;
      gap: 20px;
      
    }

    /* ul.navbar-nav-user-dropdown .breadcrumb-item:nth-child(1),
    ul.navbar-nav-user-dropdown .breadcrumb-item:nth-child(2),
    ul.navbar-nav-user-dropdown .breadcrumb-item:nth-child(3) {
      display: none;
    } */

    ul.navbar-nav-user-dropdown .breadcrumb-item+.breadcrumb-item::before {
      display: none;
    }


    a.dropdown-item {
      font-size: 12px !important;
    }

    a.dropdown-toggle p {
      margin-bottom: 0;
      margin-right: 5px;
    }

  }
</style>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item pl-3 company-logo">
      <img src="<?= BASE_URL ?>public/assets/img/logo/vitwo-logo.png" alt="">
    </li>
    <li class="nav-item ml-3 toggle-opener">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>

  </ul>



  <!-- Right navbar links -->
  <ul class="navbar-nav navbar-nav-user-dropdown">

    <li class="breadcrumb-item"><a href="" class="text-dark font-bold"><img src="<?= BASE_URL?>public/assets/img/header-icon/company.png" alt=""><?php echo $companyNameNav; ?></a></li>
    <li class="breadcrumb-item">

      <div class="dropdown">
        <a type="button" class="dropdown-toggle" data-toggle="dropdown">

          <i class="fa fa-user po-list-icon"></i>
          <p class="text-xs font-bold ml-2">
            <?= ($current_userName); ?> 
          </p>

        </a>
        <div class="dropdown-menu">
          <div class="dropdown-divider mt-1 mb-1"></div>
          <a class="dropdown-item text-danger font-bold text-center" href="<?= COMPANY_URL ?>login.php?logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
          <?php ?>
        </div>
      </div>

    </li>








  </ul>

</nav>
<!-- /.navbar -->