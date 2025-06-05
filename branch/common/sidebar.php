<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="<?= BASE_URL ?>public/assets/sidebar.css">

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #fff;">
  <!-- Brand Logo -->
  <a href="<?= BRANCH_URL ?>" class="brand-link">
    <!-- <img src="<?= BASE_URL ?>/public/storage/logo/<?= getAdministratorSettings("logo"); ?>" alt="Logo" class="brand-image"> -->
    <img src="<?= BASE_URL ?>public/assets/img/logo/vitwo-logo.png" alt="" srcset="">
    <!-- <span class="brand-text font-weight-bold"><?php //echo getAdministratorSettings("title"); 
                                                    ?>&nbsp;</span> -->
  </a>
  <?php
  if (!isset($_SESSION['menuSubMenuListObj']) || ($_SESSION['menuSubMenuListObj']['status'] != "success")) {
    $_SESSION['menuSubMenuListObj'] = getAdministratorMenuSubMenu();
    $menuSubMenuListObj = $_SESSION['menuSubMenuListObj'];
  } else {
    $menuSubMenuListObj = $_SESSION['menuSubMenuListObj'];
  }


  if (isset($menuSubMenuListObj) && $menuSubMenuListObj['status'] == "success") {
    $menuSubMenuListObj = $menuSubMenuListObj['data'];
    $lavel = strtolower($_SESSION["logedBranchAdminInfo"]["adminType"]);
    $pgUrl = '';
    if ($lavel == 'branch') {
      $pgUrl = BRANCH_URL;
    } else {
      $pgUrl = LOCATION_URL;
    }
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
                    <img src="<?= BASE_URL ?>public/assets/img/sidebar/dashboard-blue.png" title="Main" alt="Dashboard">
                  </a>
                </li>
                <?php
                $searchMM = array("../../", "../");
                foreach ($menuSubMenuListObj as $key => $grandmenu) {
                  if ($grandmenu['sidebar_view'] == 'no') {
                ?>
                    <li class="nav-item" role="presentation">
                      <a class="tablinks nav-link" href="<?= $pgUrl . $grandmenu['menuFile']; ?>?pmKey=<?= base64_encode($key); ?>">
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

                <li>
                <a class="tablinks nav-link" href="https://one.vitwo.ai/q1/api/v2/reports/auto-login.php?url=https://biw.vitwo.ai/auth">
                      <img src="<?= BASE_URL ?>public/assets/img/sidebar/analytics-menu.png" title="Logout" alt="Logout">
                    </a>
                </li>

                <!-- <?php
                if (isset($_SESSION["visitCompanyAdminInfo"]) && !isset($_SESSION["visitBranchAdminInfo"])) { ?>
                  <li class="nav-item" role="presentation">
                    <a class="tablinks nav-link" href="<?= BRANCH_URL ?>login.php?logoutCompanyFromBranch" onclick="return confirm('Go back to company profile ?')">
                      <img src="<?= BASE_URL ?>public/assets/img/sidebar/logout-red.png" title="Back To Company" alt="Logout">
                    </a>
                  </li>
                <?php } else if (isset($_SESSION["visitCompanyAdminInfo"]) && isset($_SESSION["visitBranchAdminInfo"])) { ?>
                  <li class="nav-item" role="presentation">
                    <a class="tablinks nav-link" href="<?= BRANCH_URL ?>login.php?logoutBranchFromLocation" onclick="return confirm('Go back to baranch profile ?')">
                      <img src="<?= BASE_URL ?>public/assets/img/sidebar/logout-red.png" title="Back To Branch " alt="Logout">
                    </a>
                  </li>
                <?php } else { ?>
                  <li class="nav-item" role="presentation">
                    <a class="tablinks nav-link" href="<?= BRANCH_URL ?>login.php?logout" onclick="return confirm('Are you sure to logout?')">
                      <img src="<?= BASE_URL ?>public/assets/img/sidebar/logout-red.png" title="Logout" alt="Logout">
                    </a>
                  </li>
                <?php } ?> -->
                <li>
                  <p class="release-note">
                    <a href="<?= BASE_URL?>Release_Note_ 31-05-2024.pdf" target="_blank">Release Note V.2.0 <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="15" height="15" viewBox="0 0 48 48">
                        <path fill="#e53935" d="M38,42H10c-2.209,0-4-1.791-4-4V10c0-2.209,1.791-4,4-4h28c2.209,0,4,1.791,4,4v28 C42,40.209,40.209,42,38,42z"></path>
                        <path fill="#fff" d="M34.841,26.799c-1.692-1.757-6.314-1.041-7.42-0.911c-1.627-1.562-2.734-3.45-3.124-4.101 c0.586-1.757,0.976-3.515,1.041-5.402c0-1.627-0.651-3.385-2.473-3.385c-0.651,0-1.237,0.391-1.562,0.911 c-0.781,1.367-0.456,4.101,0.781,6.899c-0.716,2.018-1.367,3.97-3.189,7.42c-1.888,0.781-5.858,2.604-6.183,4.556 c-0.13,0.586,0.065,1.172,0.521,1.627C13.688,34.805,14.273,35,14.859,35c2.408,0,4.751-3.32,6.379-6.118 c1.367-0.456,3.515-1.107,5.663-1.497c2.538,2.213,4.751,2.538,5.923,2.538c1.562,0,2.148-0.651,2.343-1.237 C35.492,28.036,35.297,27.32,34.841,26.799z M33.214,27.905c-0.065,0.456-0.651,0.911-1.692,0.651 c-1.237-0.325-2.343-0.911-3.32-1.692c0.846-0.13,2.734-0.325,4.101-0.065C32.824,26.929,33.344,27.254,33.214,27.905z M22.344,14.497c0.13-0.195,0.325-0.325,0.521-0.325c0.586,0,0.716,0.716,0.716,1.302c-0.065,1.367-0.325,2.734-0.781,4.036 C21.824,16.905,22.019,15.083,22.344,14.497z M22.214,27.124c0.521-1.041,1.237-2.864,1.497-3.645 c0.586,0.976,1.562,2.148,2.083,2.669C25.794,26.213,23.776,26.604,22.214,27.124z M18.374,29.728 c-1.497,2.473-3.059,4.036-3.905,4.036c-0.13,0-0.26-0.065-0.391-0.13c-0.195-0.13-0.26-0.325-0.195-0.586 C14.078,32.136,15.77,30.899,18.374,29.728z"></path>
                      </svg></a>
                  </p>
                </li>
              </ul>
            </aside>
            <div class="tab-content tab-content-three sidebar-collapse erp-sidebar">
              <ul class="tab-pane active" id="home" aria-labelledby="home-tab">
                <li class="menu-title menu-title-three"><span>Main</span></li>
                <li class="active">
                  <a href="<?= $pgUrl; ?>index.php"><i class="fa fa-file"></i><span>Dashboard</span></a>
                </li>
              </ul>
              <?php
              foreach ($menuSubMenuListObj as $key => $grandmenu) {
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
                        <a href="" class="dropdown-toggle <?php if ($k == 0) { ?> subdrop <?php } ?>" data-toggle="dropdown"><?= str_replace($searchMM, BASE_URL, $parentmenu['menuIcon']); ?><span><?= $parentmenu['menuLabel']; ?></span></a>
                        <ul class="sub-sub-menu" <?php if ($k == 0) { ?> style="display: block;" <?php } ?>>
                          <?php
                          if (isset($parentmenu['subMenus']) && !empty($parentmenu['subMenus'])) {
                            foreach ($parentmenu['subMenus'] as $key3 => $subMenus) {
                              // console($subMenus);
                              if ($subMenus['visibility'] == 'yes') {
                                if (basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']) == $subMenus['menuFile']) {
                          ?>
                                  <li class="submenu-sub-li active"><a href="<?= $pgUrl; ?><?= !empty($subMenus['extraPrefixFolder']) ? $subMenus['extraPrefixFolder'] : ''; ?><?= $subMenus['menuFile']; ?>"><?= str_replace($searchMM, BASE_URL, $subMenus['menuIcon']); ?><?= $subMenus['menuLabel']; ?></a></li>
                                  <script>
                                    $(document).ready(function() {
                                      // $(".parentMenuLink").removeClass("active");
                                      $("#home-tab-<?= $key ?>").click();
                                    });
                                  </script>
                                <?php
                                } else {
                                ?>
                                  <li class="submenu-sub-li"><a href="<?= $pgUrl; ?><?= !empty($subMenus['extraPrefixFolder']) ? $subMenus['extraPrefixFolder'] : ''; ?><?= $subMenus['menuFile']; ?>"><?= str_replace($searchMM, BASE_URL, $subMenus['menuIcon']); ?><?= $subMenus['menuLabel']; ?></a></li>
                          <?php
                                }
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
  <script src="<?= BASE_URL ?>public/assets-2/js/script.js"></script>
</aside>
<!-- /.Main Sidebar Container ---->
<?php require_once("mobile-menu.php");
?>