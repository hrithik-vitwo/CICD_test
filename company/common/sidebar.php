<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- <link rel="stylesheet" href="../public/assets-2/css/style.css"> -->
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/feather.css">

<link rel="stylesheet" href="<?= BASE_URL ?>public/assets-2/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets-2/plugins/fontawesome/css/all.min.css">

<style>
  .layout-fixed .wrapper .sidebar {
    width: 100%;
    top: 0;
  }

  body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
  body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer,
  body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header {
    margin-left: 270px;
  }

  .sidebar.opened {
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    transition: all 0.4s ease;
  }

  .main-sidebar,
  .sidebar-mini.sidebar-collapse .main-sidebar.sidebar-focused,
  .sidebar-mini.sidebar-collapse .main-sidebar:hover {
    width: 270px;
  }

  .sidebar-mini.sidebar-collapse.layout-fixed .main-sidebar .brand-link,
  .sidebar-mini.sidebar-collapse.layout-fixed .main-sidebar:hover .brand-link {
    width: 100%;
  }

  .sidebar-collapse.sidebar-mini .main-sidebar.sidebar-focused .nav-link,
  .sidebar-collapse.sidebar-mini .main-sidebar:hover .nav-link,
  .sidebar-collapse.sidebar-mini-md .main-sidebar.sidebar-focused .nav-link,
  .sidebar-collapse.sidebar-mini-md .main-sidebar:hover .nav-link,
  .sidebar-collapse.sidebar-mini-xs .main-sidebar.sidebar-focused .nav-link,
  .sidebar-collapse.sidebar-mini-xs .main-sidebar:hover .nav-link {
    width: 100%;
  }

  .sidebar-mini .main-sidebar .nav-link,
  .sidebar-mini-md .main-sidebar .nav-link,
  .sidebar-mini-xs .main-sidebar .nav-link {
    width: 100%;
  }

  .sidebar-inner {
    height: 100%;
    min-height: 100%;
    transition: all 0.2s ease-in-out 0s;
  }

  .sidebar-menu ul {

    font-size: 15px;

    list-style-type: none;

    margin: 0;

    padding: 15px 0;

    position: relative;

    justify-content: center;

    gap: 0px;

  }

  .sidebar-menu li a {
    color: #95979b;
    display: block;
    font-size: 15px;
    height: auto;
    padding: 0 20px;

  }

  /* .sidebar-menu li a:hover {
    color: #7638ff;
  } */

  .sidebar-menu li a:hover img {
    filter: grayscale(0);
  }

  .sidebar-menu>ul>li>a:hover {
    background-color: rgba(118, 56, 255, 0.05);
    color: #7638ff;
  }

  .sidebar-menu li.active>a {
    background-color: rgba(118, 56, 255, 0.05);
    color: #7638ff;
    position: relative;
    width: 100%;
  }

  .sidebar-menu li.active>a::before {
    width: 5px;
    content: "";
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background: #7638ff;
    -webkit-transition: all 0.5s;
    -ms-transition: all 0.5s;
    transition: all 0.5s;
  }

  .menu-title {
    color: #9e9e9e;
    display: flex;
    font-size: 14px;
    opacity: 1;
    padding: 5px 15px;
    white-space: nowrap;
  }

  .menu-title>i {
    float: right;
    line-height: 40px;
  }

  .sidebar-menu li.menu-title a {
    color: #ff9b44;
    display: inline-block;
    margin-left: auto;
    padding: 0;
  }

  .sidebar-menu li.menu-title a.btn {
    color: #fff;
    display: block;
    float: none;
    font-size: 15px;
    margin-bottom: 15px;
    padding: 10px 15px;
  }

  .sidebar-menu ul ul a.active {
    color: #7638ff;
  }

  .mobile_btn {
    display: none;
    float: left;
  }

  .sidebar .sidebar-menu>ul>li>a span {
    transition: all 0.2s ease-in-out 0s;
    display: inline-block;
    margin-left: 10px;
    white-space: nowrap;
  }

  .sidebar .sidebar-menu>ul>li>a span.chat-user {
    margin-left: 0;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .sidebar .sidebar-menu>ul>li>a span.badge {
    margin-left: auto;
  }

  .sidebar-menu ul ul a {
    display: block;
    font-size: 15px;
    padding: 7px 10px 7px 45px;
    position: relative;
  }

  .sidebar-menu ul ul a span {
    float: right;
  }

  .sidebar-menu ul ul {
    display: none;
  }

  .sidebar-menu ul ul ul a {
    padding-left: 65px;
  }

  .sidebar-menu ul ul ul ul a {
    padding-left: 85px;
  }

  .sidebar-menu>ul>li {
    margin-bottom: 3px;
    position: relative;
  }

  .sidebar-menu>ul>li:last-child {
    margin-bottom: 25px;
  }

  .sidebar-menu .menu-arrow {
    -webkit-transition: -webkit-transform 0.15s;
    -o-transition: -o-transform 0.15s;
    transition: transform .15s;
    position: absolute;
    right: 15px;
    display: inline-block;
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    text-rendering: auto;
    line-height: 40px;
    font-size: 16px;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    -webkit-transform: translate(0, 0);
    -transform: translate(0, 0);
    -o-transform: translate(0, 0);
    transform: translate(0, 0);
    line-height: 18px;
    top: 11px;
  }

  .sidebar-menu .menu-arrow:before {
    content: "\f105";
  }

  .sidebar-menu li a.subdrop .menu-arrow {
    -transform: rotate(90deg);
    -webkit-transform: rotate(90deg);
    -o-transform: rotate(90deg);
    transform: rotate(90deg);
  }

  .sidebar-menu ul ul a .menu-arrow {
    top: 10px;
  }

  .sidebar-menu>ul>li>a {
    align-items: center;
    display: flex;
    justify-content: flex-start;
    padding: 8px 15px;
    position: relative;
    transition: all 0.2s ease-in-out 0s;
  }

  .sidebar-menu>ul>li>a svg {
    width: 18px;
  }

  .sidebar-menu ul li a i {
    display: inline-block;
    font-size: 16px;
    line-height: 24px;
    text-align: left;
    vertical-align: middle;
    width: 20px;
    transition: all 0.2s ease-in-out 0s;
  }

  .sidebar-menu ul li.menu-title a i {
    font-size: 16px !important;
    margin-right: 0;
    text-align: right;
    width: auto;
  }

  .sidebar-menu li a>.badge {
    color: #fff;
  }

  .sidebar-two {
    background-color: #101924;
    top: 60px;
    border-top-right-radius: 0px;
  }

  .menu-title-two {
    color: #fff;
  }

  .sidebar-menu-two {
    padding: 20px 0px 0px 0px;
  }

  .sidebar-menu-two li.active>a {
    background-color: rgb(246 247 249);
  }

  .sidebar-menu-two li a {
    color: #6E82A5;
    display: block;
    font-size: 15px;
    height: auto;
    padding: 0 20px;
  }

  .sidebar-menu-two>ul>li>a:hover {
    background-color: rgb(247 248 249);
    color: #7638ff;
  }

  .sidebar-three {
    top: 0px;
    border-top-right-radius: 0px;
    width: 100%;
    padding: 0;
  }

  /* .sidebar-bg {
    background-color: #01202f !important;
  } */

  .sidebar-three-three {
    left: unset;
  }

  .menu-title-three {
    color: #757575;
  }

  .sidebar-menu-three {
    height: 100%;
    border-bottom: none;
  }

  .sidebar-menu-three>ul {
    border-bottom: none;
  }

  .sidebar-menu-three li.active>a {
    background-color: #fff !important;
    white-space: pre-line;
    border-radius: 0;
  }

  .sidebar-menu-three li>a {
    color: #6E82A5;
    display: block;
    font-size: 15px;
    height: auto;
    padding: 9px 7px;
  }

  .sidebar-menu-three>ul>li>a:hover {
    background: rgba(118, 56, 255, 0.12);
    color: #7638ff;
  }

  .sidebar-menu-three li.active>a::before {
    right: 0;
    left: auto;
    background: #004b98;
  }

  .sidebar-menu-three ul ul {
    padding: 0;
  }

  .sidebar-menu-three ul ul li a {
    padding-left: 25px;
    display: inline-block;
  }

  .ui-aside-compact .ui-aside {
    margin-left: 0;
    -webkit-transition: all .301s;
    transition: all .301s;
  }

  .ui-aside {

    float: left;

    width: 75px;

    margin-left: 0px;

    color: rgba(255, 255, 255, 0.5);

    -webkit-transition: all .299s;

    transition: all .299s;

    padding: 0px 0px;

    margin-right: 0px;

  }

  .ui-aside::before {

    content: "";

    position: absolute;

    top: 0;

    bottom: 0;

    width: inherit;

    background-color: #cfd8e1;

    border: inherit;

    z-index: -1;

    background-size: cover;

    border-right: 1px solid #e3e3e3;

  }

  .tab-content>.tab-pane li {
    margin-left: 5px;
  }


  .tab-content li a span,
  .tab-content li a i {
    font-size: 13px;
    font-weight: 600;
    color: #004b98;
  }

  .tab {
    border-bottom: none;
  }


  .tab .tablinks {
    display: block;
    background-color: transparent;
    color: #6e82a5;
    padding: 7px 10px !important;
    width: 100%;
    border: none;
    outline: none;
    text-align: center;
    cursor: pointer;
    position: relative;
    z-index: 1;
    transition: 0.3s;
    margin: 10px 0px;
    font-size: 15px;
    border-radius: 7px;
  }

  .tab .tablinks.active {
    color: #fff;
    background-color: #ffffff7d;
    border-color: #182b3e #182b3e #182b3e;
  }

  .tab .tablinks.active:after {
    width: 5px;
    content: "";
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background: #7638ff;
    -webkit-transition: all 0.5s;
    -ms-transition: all 0.5s;
    transition: all 0.5s;
  }

  .tab .tablinks:hover {
    color: #fff;
    background-color: #ffffff7d;
  }

  .tab .tablinks i img {
    width: 16px;
  }

  .tab .tablinks i img:hover {
    filter: invert(1) brightness(100);
    transform: scale(1);
    -webkit-transform: scale(1);
    -moz-transform: scale(1);
    -ms-transform: scale(1);
    -o-transform: scale(1);
  }

  .tab .tablinks.active>i img {
    filter: invert(1) brightness(100);
    transform: scale(1);
    -webkit-transform: scale(1);
    -moz-transform: scale(1);
    -ms-transform: scale(1);
    -o-transform: scale(1);
  }

  .tab .tablinks .active {
    background-color: #ccc;
  }

  .tab .tablinks .active::before {
    width: 5px;
    content: "";
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background: #7638ff;
    -webkit-transition: all 0.5s;
    -ms-transition: all 0.5s;
    transition: all 0.5s;
  }

  .tab .tablinks .active::before {
    width: 5px;
    content: "";
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
    background: #7638ff;
    -webkit-transition: all 0.5s;
    -ms-transition: all 0.5s;
    transition: all 0.5s;
  }

  .tab-content-three {
    margin-left: 11px;
    padding-top: 20px;
    padding-left: 62px;
  }

  .sidebar-menu-three .menu-arrow {
    top: auto;
  }

  .sidebar-four {
    background-color: #101924;
    top: 60px;
    border-top-right-radius: 0px;
  }

  .sidebar-menu-four>ul>li>a {
    padding: 9px 15px;
  }

  .sidebar-menu-four li a {
    color: #6E82A5;
    display: block;
    font-size: 15px;
    height: auto;
    padding: 0 20px;
  }

  .sidebar-menu-four>ul>li>a:hover {
    background-color: rgb(247 248 249);
    color: #7638ff;
  }

  .menu-title-four {
    color: #fff;
  }

  .sidebar-five {
    background-color: transparent;
    top: 0px;
    border-top-right-radius: 0px;
    float: left;
    margin: 0;
    position: relative;
    z-index: 99;
    width: auto;
    overflow-y: visible;
    box-shadow: none;
  }

  .sidebar-menu-five ul {
    padding: 10px 0;
    position: relative;
    display: flex;
  }

  .sidebar-menu-five ul .dropdown-menu-right {
    position: absolute;
    width: 220px;
    height: auto;
    border: none;
  }

  .sidebar-menu-five>ul>li {
    margin-bottom: 0px;
    position: relative;
  }

  .sidebar-menu-five>ul>li:last-child {
    margin-bottom: 0px;
  }

  .sidebar-menu-five li.active>a {
    background-color: rgb(247 248 249);
    color: #7638ff;
    position: relative;
  }

  .sidebar-menu-five ul ul a span {
    transition: all 0.2s ease-in-out 0s;
    display: inline-block;
    margin-left: 10px;
    white-space: nowrap;
    float: unset;
  }

  .sidebar-menu-five>ul>li>a {
    color: #fff;
    z-index: 9999;
  }

  .sidebar-menu-five li a:hover {
    color: #7638ff;
    background-color: #fff;
  }

  .sidebar-menu-five>ul>li>a:hover {
    background-color: rgb(16 25 36);
    color: #fff;
    border-bottom: 3px solid #7638FF;
  }

  .sidebar-menu-five .menu-arrow {
    transform: rotate(90deg);
    position: initial;
  }

  .sidebar-menu-five li a.subdrop .menu-arrow {
    -webkit-transform: rotate(-90deg);
    transform: rotate(-90deg);
    transform: rotate(-90deg);
  }

  .sidebar-menu-five ul ul a:hover {
    background-color: rgba(118, 56, 255, 0.05);
    color: #7638ff;
  }

  .header .dropdown-menu-five>li>a:focus,
  .header .dropdown-menu-five>li>a:hover {
    background-color: rgb(246 247 249);
    color: #7638ff;
  }

  .header .nav-tabs {
    border: 0;
  }

  .tab .tablinks img {
    width: 25px;
  }

  body.sidebar-mini.layout-fixed a.brand-link img {
    width: 100%;
    max-width: 100px;
  }

  .layout-fixed .brand-link {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: 56.5px;
    border-color: #bec1c5;
  }

  li.submenu a.subdrop:nth-child(2)::before {
    content: "▼";
    padding: 12px 8px;
    position: absolute;
    right: 10px;
    top: 0;
    z-index: 1;
    text-align: center;
    width: 10%;
    height: 100%;
    pointer-events: none;
  }

  .sidebar-menu ul ul a {
    display: block;
    font-size: 13px !important;
    padding: 7px 0px 7px 8px !important;
    position: relative;
  }

  /* .tab .tablinks {
    display: block !important;
    background-color: #001621 !important;
    color: #6e82a5 !important;
    padding: 10px !important;
    width: 100% !important;
    border: none !important;
    outline: none !important;
    text-align: center !important;
    cursor: pointer !important;
    position: relative !important;
    z-index: 1 !important;
    transition: 0.3s !important;
    margin: 10px 3px !important;
    font-size: 15px !important;
    border-radius: 7px;
  } */

  .sidebar-menu-three li>a {

    color: #8d8d8d;

    display: block;

    font-size: 13px;

    padding: 9px 7px;

    display: flex !important;

    align-items: center;

    justify-content: flex-start;

    gap: 7px;

    margin: 0;

    background: rgb(0 22 33);

    border-radius: 0;

    top: 10px;

    left: 0px !important;

    width: 100%;

    text-decoration: none;
  }

  ul.sub-sub-menu li.submenu ul li a {

    white-space: pre-line;

    position: relative;

    left: 0 !important;

    top: 12px;

    width: 80%;

    background: none;

    display: flex !important;

    align-items: center;

    justify-content: flex-start;

    gap: 10px;

    font-size: 10px !important;

    text-decoration: none;

    margin: 0;

    border-radius: 0;

  }

  [class*=sidebar-dark-] .sidebar a {

    color: #565656;

    top: 0 !important;

    height: 40px;

  }

  li.submenu ul.sub-sub-menu li a img {
    filter: grayscale(1);
  }

  li.submenu ul li.active a img,
  li.submenu ul li.active a span,
  li.submenu ul li.active a:hover img,
  li.submenu ul li.active a:hover span {
    filter: grayscale(0);
  }

  li.submenu ul.sub-sub-menu li a {
    font-size: 11px !important;
  }


  li.submenu ul.sub-sub-menu li.active a {

    color: #003060;
    background-color: #00306030 !important;
    margin: 0px auto;
    border-bottom: 0;
    width: 90%;
    border-radius: 12px;
  }

  @media (max-width: 991.98px) {
    .sidebar-three {
      width: 100%;
      margin-left: 0 !important;
    }
  }

  @media (max-width: 768x) {
    .main-sidebar {
      margin-left: -280px;
    }
  }
</style>


<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #fff;">
  <!-- Brand Logo -->
  <a href="<?= COMPANY_URL ?>" class="brand-link">
    <!-- <img src="<?= BASE_URL ?>/public/storage/logo/<?= getAdministratorSettings("logo"); ?>" alt="Logo" class="brand-image"> -->
    <img src="<?= BASE_URL ?>public/assets/img/logo/vitwo-logo.png" alt="" srcset="">
    <!-- <span class="brand-text font-weight-bold"><?php //echo getAdministratorSettings("title"); 
                                                    ?>&nbsp;</span> -->
  </a>
  <?php
  //$jf=getAdministratorMenuSubMenu();
  // console($_SESSION);
  // console($jf);
  if (!isset($_SESSION['menuSubMenuListObjCompany']) || ($_SESSION['menuSubMenuListObjCompany']['status'] != "success")) {
    $_SESSION['menuSubMenuListObjCompany'] = getAdministratorMenuSubMenu();
    $menuSubMenuListObjCompany = $_SESSION['menuSubMenuListObjCompany'];
  } else {
    $menuSubMenuListObjCompany = $_SESSION['menuSubMenuListObjCompany'];
  }
  if (isset($menuSubMenuListObjCompany) && $menuSubMenuListObjCompany['status'] == "success") {
    $menuSubMenuListObjCompany = $menuSubMenuListObjCompany['data'];
  ?>

    <div class="main-wrapper">
      <div class="sidebar sidebar-bg sidebar-three sidebar-collapse" id="sidebar">
        <div class="sidebar-inner slimscroll">
          <div id="sidebar-menu" class="sidebar-menu sidebar-menu-three">
            <aside id="aside" class="ui-aside">
              <ul class="tab nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <a class="tablinks nav-link parentMenuLink active" href="#home" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" role="tab" aria-controls="home" aria-selected="true">
                    <!-- <i class="fa fa-list" style="color: #fff;"></i> -->
                    <img src="<?= BASE_URL ?>public/assets/img/sidebar/dashboard.png" title="Main" alt="Dashboard">
                  </a>
                </li>
                <?php                
                $searchMM = array("../../", "../");
                foreach ($menuSubMenuListObjCompany as $key => $grandmenu) {
                  if($grandmenu['sidebar_view']=='no'){
                    ?>
                      <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link" href="<?= COMPANY_URL.$grandmenu['menuFile']; ?>?pmKey=<?= base64_encode($key);?>">
                        <?= str_replace($searchMM, BASE_URL, $grandmenu['menuIcon']); ?>
                        </a>
                      </li>
                      <?php } else { ?>
                      <li class="nav-item" role="presentation">
                        <a class="tablinks nav-link parentMenuLink" href="#home<?= $key; ?>" id="home-tab-<?= $key; ?>" data-bs-toggle="tab" data-bs-target="#home<?= $key; ?>" role="tab" aria-controls="home<?= $key; ?>" aria-selected="true">
                          <!-- <i class="fa fa-list" style="color: #fff;"></i> -->
                          <!--<img src="../public/assets/img/sidebar/sales-and-distribution.png" title="Sales and Distribution" alt="sales_and_distribution">-->
                          
                          <?= str_replace($searchMM, BASE_URL, $grandmenu['menuIcon']); ?>
                        </a>
                      </li>
                    <?php } 
                   } ?>

                <?php
                if (isset($_SESSION["visitCompanyAdminInfo"]) && !isset($_SESSION["visitBranchAdminInfo"])) { ?>
                  <li class="nav-item" role="presentation">
                    <a class="tablinks nav-link" href="<?= COMPANY_URL ?>login.php?logoutCompanyFromBranch" onclick="return confirm('Go back to company profile ?')">

                      <img src="<?= BASE_URL ?>public/assets/img/sidebar/logout.png" title="Back To Company" alt="Logout">
                    </a>
                  </li>
                <?php } else if (isset($_SESSION["visitCompanyAdminInfo"]) && isset($_SESSION["visitBranchAdminInfo"])) { ?>
                  <li class="nav-item" role="presentation">
                    <a class="tablinks nav-link" href="<?= COMPANY_URL ?>login.php?logoutBranchFromLocation" onclick="return confirm('Go back to baranch profile ?')">

                      <img src="<?= BASE_URL ?>public/assets/img/sidebar/logout.png" title="Back To Branch " alt="Logout">
                    </a>
                  </li>
                <?php } else { ?>
                  <li class="nav-item" role="presentation">
                    <a class="tablinks nav-link" href="<?= COMPANY_URL ?>login.php?logout" onclick="return confirm('Are you sure to logout?')">

                      <img src="<?= BASE_URL ?>public/assets/img/sidebar/logout.png" title="Logout" alt="Logout">
                    </a>
                  </li>
                <?php } ?>
              </ul>
            </aside>
            <div class="tab-content tab-content-three sidebar-collapse">
              <ul class="tab-pane active" id="home" aria-labelledby="home-tab">
                <li class="menu-title menu-title-three"><span>Main</span></li>
                <li class="active">
                  <a href="index.php"><i class="fa fa-file"></i><span>Dashboard</span></a>
                </li>
              </ul>
              <?php
              foreach ($menuSubMenuListObjCompany as $key => $grandmenu) {
              ?>
                <ul class="tab-pane" id="home<?= $key; ?>" aria-labelledby="home-tab-<?= $key; ?>">
                  <li class="menu-title menu-title-three active"><span><?= $grandmenu['menuLabel']; ?></span></li>
                  <!--<li class="active">
                <a href="index-2.html"><i class="fa fa-file"></i><span>Sales order</span></a>
              </li>-->
                  <?php
                  if (isset($grandmenu['subParentMenus']) && !empty($grandmenu['subParentMenus'])) {
                    $k = 0;
                    foreach ($grandmenu['subParentMenus'] as $key2 => $parentmenu) {
                  ?>
                      <li class="submenu active">
                        <a href="" class="dropdown-toggle <?php if ($k == 0) { ?> subdrop <?php } ?>" data-toggle="dropdown"> <?= str_replace($searchMM, BASE_URL, $parentmenu['menuIcon']); ?><span><?= $parentmenu['menuLabel']; ?></span>

                        </a>
                        <ul class="sub-sub-menu" <?php if ($k == 0) { ?> style="display: block;" <?php } ?>>
                          <?php
                          if (isset($parentmenu['subMenus']) && !empty($parentmenu['subMenus'])) {
                            foreach ($parentmenu['subMenus'] as $key3 => $subMenus) {
                              if (basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']) == $subMenus['menuFile']) {
                          ?>
                                <li class="submenu-sub-li active"><a href="<?= $subMenus['menuFile']; ?>"> <?= str_replace($searchMM, BASE_URL, $subMenus['menuIcon']); ?><?= $subMenus['menuLabel']; ?></a></li>
                                <script>
                                  $(document).ready(function() {
                                    // $(".parentMenuLink").removeClass("active");
                                    $("#home-tab-<?= $key ?>").click();
                                  });
                                </script>
                              <?php
                              } else {
                              ?>
                                <li class="submenu-sub-li"><a href="<?= $subMenus['menuFile']; ?>"> <?= str_replace($searchMM, BASE_URL, $subMenus['menuIcon']); ?><?= $subMenus['menuLabel']; ?></a></li>
                          <?php
                              }
                            }
                          } ?>
                        </ul>
                      </li>
                  <?php $k++;
                    }
                  } ?>
                </ul>
              <?php } ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <!-- <script>
    $(document).ready(function() {
      if ($(".submenu-sub-li a").attr("href") == window.location.href) {
        $(".submenu-sub-li").attr("class", "submenu-sub-li active");
      } else {
        $(".submenu-sub-li").attr("class", "submenu-sub-li");
      }
    });
  </script> -->

  <script src="<?= BASE_URL ?>public/assets-2/js/script.js"></script>


</aside>





<!-- /.Main Sidebar Container ---->
<?php require_once("mobile-menu.php");

/*$company_id = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"] ?? "";
$branch_id = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] ?? "";
$location_id = $_SESSION["logedBranchAdminInfo"]["fldAdminLocationId"] ?? "";*/
?>