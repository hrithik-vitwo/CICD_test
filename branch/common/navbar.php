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
    right: 100px;
    position: relative;
  }

  body.sidebar-mini.layout-fixed .navbar-nav-user-dropdown {
    right: 300px;
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
    left: -44px !important;
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
    }

    ul.navbar-nav-user-dropdown .breadcrumb-item:nth-child(1),
    ul.navbar-nav-user-dropdown .breadcrumb-item:nth-child(2),
    ul.navbar-nav-user-dropdown .breadcrumb-item:nth-child(3) {
      display: none;
    }

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


  .main-header .status-bg {
    padding: 5px 17px;
    border-radius: 7px;
    font-size: 0.7rem;
    font-weight: 500;
    text-align: center;
    position: relative;
    left: -140px;
    display: inline-block;
    width: auto;
  }

  body.sidebar-collapse .main-header .status-bg {
    left: 0;
  }

  .main-header .status-bg a {
    font-weight: 600;
    text-decoration: underline;
  }

  .main-header .status-bg a:hover {
    color: #884f00; 
  }

  .main-header .status-warning {
    background: #ffc4044f;
    color: #884f00;
  }

  .main-header .status-warning::before {
    content: '';
    position: relative;
    top: -1px;
    left: -8px;
    display: inline-block;
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background-color: #ffb100;
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
<!-- Relese Note Section -->
<!-- <?php
if (strpos(BASE_URL, ".ai/a2/") != false) { ?>
  <p class="status-bg status-warning">Back to Classic UI <a href="<?=BRANCH_URL?>switch.php?v=1">Click here</a>. &nbsp;<a target="_blank" href="<?= BASE_URL?>Release_Note_ 31-05-2024.pdf"><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="15" height="15" viewBox="0 0 48 48">
                        <path fill="#e53935" d="M38,42H10c-2.209,0-4-1.791-4-4V10c0-2.209,1.791-4,4-4h28c2.209,0,4,1.791,4,4v28 C42,40.209,40.209,42,38,42z"></path>
                        <path fill="#fff" d="M34.841,26.799c-1.692-1.757-6.314-1.041-7.42-0.911c-1.627-1.562-2.734-3.45-3.124-4.101 c0.586-1.757,0.976-3.515,1.041-5.402c0-1.627-0.651-3.385-2.473-3.385c-0.651,0-1.237,0.391-1.562,0.911 c-0.781,1.367-0.456,4.101,0.781,6.899c-0.716,2.018-1.367,3.97-3.189,7.42c-1.888,0.781-5.858,2.604-6.183,4.556 c-0.13,0.586,0.065,1.172,0.521,1.627C13.688,34.805,14.273,35,14.859,35c2.408,0,4.751-3.32,6.379-6.118 c1.367-0.456,3.515-1.107,5.663-1.497c2.538,2.213,4.751,2.538,5.923,2.538c1.562,0,2.148-0.651,2.343-1.237 C35.492,28.036,35.297,27.32,34.841,26.799z M33.214,27.905c-0.065,0.456-0.651,0.911-1.692,0.651 c-1.237-0.325-2.343-0.911-3.32-1.692c0.846-0.13,2.734-0.325,4.101-0.065C32.824,26.929,33.344,27.254,33.214,27.905z M22.344,14.497c0.13-0.195,0.325-0.325,0.521-0.325c0.586,0,0.716,0.716,0.716,1.302c-0.065,1.367-0.325,2.734-0.781,4.036 C21.824,16.905,22.019,15.083,22.344,14.497z M22.214,27.124c0.521-1.041,1.237-2.864,1.497-3.645 c0.586,0.976,1.562,2.148,2.083,2.669C25.794,26.213,23.776,26.604,22.214,27.124z M18.374,29.728 c-1.497,2.473-3.059,4.036-3.905,4.036c-0.13,0-0.26-0.065-0.391-0.13c-0.195-0.13-0.26-0.325-0.195-0.586 C14.078,32.136,15.77,30.899,18.374,29.728z"></path>
                      </svg>What's New </a></p>
  <?php }else{?>
  <p class="status-bg status-warning">IMPORTANT: Vitwo.ai has been updated to a newer version. <a href="<?=BRANCH_URL?>switch.php?v=2">Click here</a> to update. &nbsp;<a target="_blank" href="<?= BASE_URL?>Release_Note_ 31-05-2024.pdf"><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="15" height="15" viewBox="0 0 48 48">
                        <path fill="#e53935" d="M38,42H10c-2.209,0-4-1.791-4-4V10c0-2.209,1.791-4,4-4h28c2.209,0,4,1.791,4,4v28 C42,40.209,40.209,42,38,42z"></path>
                        <path fill="#fff" d="M34.841,26.799c-1.692-1.757-6.314-1.041-7.42-0.911c-1.627-1.562-2.734-3.45-3.124-4.101 c0.586-1.757,0.976-3.515,1.041-5.402c0-1.627-0.651-3.385-2.473-3.385c-0.651,0-1.237,0.391-1.562,0.911 c-0.781,1.367-0.456,4.101,0.781,6.899c-0.716,2.018-1.367,3.97-3.189,7.42c-1.888,0.781-5.858,2.604-6.183,4.556 c-0.13,0.586,0.065,1.172,0.521,1.627C13.688,34.805,14.273,35,14.859,35c2.408,0,4.751-3.32,6.379-6.118 c1.367-0.456,3.515-1.107,5.663-1.497c2.538,2.213,4.751,2.538,5.923,2.538c1.562,0,2.148-0.651,2.343-1.237 C35.492,28.036,35.297,27.32,34.841,26.799z M33.214,27.905c-0.065,0.456-0.651,0.911-1.692,0.651 c-1.237-0.325-2.343-0.911-3.32-1.692c0.846-0.13,2.734-0.325,4.101-0.065C32.824,26.929,33.344,27.254,33.214,27.905z M22.344,14.497c0.13-0.195,0.325-0.325,0.521-0.325c0.586,0,0.716,0.716,0.716,1.302c-0.065,1.367-0.325,2.734-0.781,4.036 C21.824,16.905,22.019,15.083,22.344,14.497z M22.214,27.124c0.521-1.041,1.237-2.864,1.497-3.645 c0.586,0.976,1.562,2.148,2.083,2.669C25.794,26.213,23.776,26.604,22.214,27.124z M18.374,29.728 c-1.497,2.473-3.059,4.036-3.905,4.036c-0.13,0-0.26-0.065-0.391-0.13c-0.195-0.13-0.26-0.325-0.195-0.586 C14.078,32.136,15.77,30.899,18.374,29.728z"></path>
                      </svg>What's New</a></p>
  <?php } ?> -->


  <!-- Right navbar links -->
  <ul class="navbar-nav navbar-nav-user-dropdown">

    <li class="breadcrumb-item"><a href="" class="text-dark font-bold"><img src="<?= BASE_URL ?>public/assets/img/header-icon/company.png" alt=""><?php echo $companyNameNav; ?></a></li>

    <li class="breadcrumb-item"><a href="" class="text-dark font-bold"><img src="<?= BASE_URL ?>public/assets/img/header-icon/branch.png" alt=""><?php echo $branchNameNav; ?></a></li>
    <?php if ($_SESSION["logedBranchAdminInfo"]["adminType"] == 'location') { ?>
      <li class="breadcrumb-item"><a class="text-dark font-bold"><img src="<?= BASE_URL ?>public/assets/img/header-icon/location.png" alt=""><?php echo $locationNameNav; ?></a></li>
    <?php } ?>
    <li class="breadcrumb-item">

      <div class="dropdown">
        <a type="button" class="dropdown-toggle" data-toggle="dropdown">

          <i class="fa fa-user po-list-icon"></i>
          <p class="text-xs font-bold ml-2">
            <?= ($current_userName); ?>
          </p>

        </a>
        <div class="dropdown-menu">
          <?php if (isset($_SESSION["visitCompanyAdminInfo"]) && !isset($_SESSION["visitBranchAdminInfo"])) { ?>
            <a class="dropdown-item" href="<?= BRANCH_URL ?>login.php?logoutCompanyFromBranch"><i class="fa fa-sign-out" aria-hidden="true"></i> Back to company</a>
          <?php } else if (isset($_SESSION["visitCompanyAdminInfo"]) && isset($_SESSION["visitBranchAdminInfo"])) { ?>
            <a class="dropdown-item" href="<?= BRANCH_URL ?>login.php?logoutBranchFromLocation"><i class="fa fa-sign-out" aria-hidden="true"></i> Back to branch</a>
          <?php } else if (!isset($_SESSION["visitCompanyAdminInfo"]) && isset($_SESSION["visitBranchAdminInfo"])) { ?>
            <a class="dropdown-item" href="<?= BRANCH_URL ?>login.php?logoutBranchFromLocation"><i class="fa fa-sign-out" aria-hidden="true"></i> Back to branch</a>
          <?php } else {
          }
          ?>
          <div class="dropdown-divider mt-1 mb-1"></div>
          <a class="dropdown-item font-bold text-center" href="my-tickets.php"><i class="fa fa-bitbucket"></i> My Tickets</a>
          <div class="dropdown-divider mt-1 mb-1"></div>
          <a class="dropdown-item text-danger font-bold text-center" href="<?= BRANCH_URL ?>login.php?logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
          <?php ?>
        </div>
      </div>

    </li>

    <!-- <div class="dropdown">
      <span type="button" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-user po-list-icon-invert"></i> <?= strtoupper($_SESSION["logedBranchAdminInfo"]["adminType"]); ?> USER
      </span>
      <div class="dropdown-menu">
       
        <div class="dropdown-divider"></div>
        <?php if (isset($_SESSION["visitCompanyAdminInfo"]) && !isset($_SESSION["visitBranchAdminInfo"])) { ?>
          <a class="dropdown-item" href="<?= BRANCH_URL ?>login.php?logoutCompanyFromBranch"><i class="fa fa-sign-out" aria-hidden="true"></i> Back to company</a>
        <?php } else if (isset($_SESSION["visitCompanyAdminInfo"]) && isset($_SESSION["visitBranchAdminInfo"])) { ?>
          <a class="dropdown-item" href="<?= BRANCH_URL ?>login.php?logoutBranchFromLocation"><i class="fa fa-sign-out" aria-hidden="true"></i> Back to branch</a>
        <?php } else if (!isset($_SESSION["visitCompanyAdminInfo"]) && isset($_SESSION["visitBranchAdminInfo"])) { ?>
          <a class="dropdown-item" href="<?= BRANCH_URL ?>login.php?logoutBranchFromLocation"><i class="fa fa-sign-out" aria-hidden="true"></i> Back to branch</a>
        <?php } else {
        }
        ?>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= BRANCH_URL ?>login.php?logout"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
        <?php ?>
      </div>
    </div> -->
    <!-- Navbar Search -->
    <!-- <li class="nav-item">
        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>
      </li> -->

    <!-- Notifications Dropdown Menu -->
    <!-- <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li> -->
    <!-- <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li> -->
  </ul>
</nav>
<!-- /.navbar -->