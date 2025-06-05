<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background-color: #003060;">
  <!-- Brand Logo -->
  <a href="<?= CUSTOMER_URL ?>" class="brand-link">
    <img src="<?= BASE_URL ?>/public/storage/logo/<?= getAdministratorSettings("logo"); ?>" alt="Logo" class="brand-image " style="width: 152px;">
    <span class="brand-text font-weight-bold"><?php //echo getAdministratorSettings("title"); ?>&nbsp;</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="<?= CUSTOMER_URL ?>" class="nav-link">
            <i class="nav-icon fas fa-th"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>
        <?php
        $menuSubMenuListObj = getAdministratorMenuSubMenu();
        if($menuSubMenuListObj["status"]=="success"){
          ?>
          <li class="nav-header">Menu</li>
          <?php
          foreach($menuSubMenuListObj["data"] as $oneMenu){
            ?>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <!-- <i class="nav-icon fas fa-eye"></i> -->
                <img width="20" src="../public/storage/icons/folder.png" alt="icons">
                <p>
                  <?= $oneMenu["menuLabel"] ?>
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <?php
                foreach($oneMenu["subMenus"] as $oneSubMenu){
                  ?>
                    <li class="nav-item">
                      <a href="<?= $oneSubMenu["menuFile"] ?>" class="nav-link">
                        <!-- <i class="far fa-circle nav-icon"></i> -->
                        <?= $oneSubMenu["menuIcon"] ?>
                        <p><?= $oneSubMenu["menuLabel"] ?></p>
                      </a>
                    </li>
                  <?php
                }
                ?>
              </ul>
            </li>
            <?php
          }
        }

        if ($_SESSION["logedCustomerAdminInfo"]["adminRole"] == 1 ) {
          ?>
            <li class="nav-header">Settings</li>
            <li class="nav-item">
              <a href="#" class="nav-link">
              <img width="20" src="../public/storage/icons/folder.png" alt="icons">
                <p>
                  Administrator
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="administrator-setting.php" class="nav-link">
                    <img width="20" src="../public/storage/icons/google-sheets.png" alt="icons">
                    <p>Manage Settings</p>
                  </a>
                </li>
               <!-- <li class="nav-item">
                  <a href="administrator-role.php" class="nav-link">
                  <img width="20" src="../public/storage/icons/google-sheets.png" alt="icons">
                    <p>Manage Roles</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="administrator-user.php" class="nav-link">
                    <img width="20" src="../public/storage/icons/google-sheets.png" alt="icons">
                    <p>Manage Admin Users</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="administrator-menu.php" class="nav-link">
                    <img width="20" src="../public/storage/icons/google-sheets.png" alt="icons">
                    <p>Manage Menu</p>
                  </a>
                </li>-->
              </ul>
            </li>
          <?php
          }
          
        ?>
        <!-- <li class="nav-header">Others</li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-edit"></i>
            <p>
              Empty Pages
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="empty-page.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Empty Page</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="empty-form.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Empty Form</p>
              </a>
            </li>

          </ul>
        </li> -->
        <li class="nav-header">Logout</li>
        <li class="nav-item">
          <a href="<?= CUSTOMER_URL ?>login.php?logout" onclick="return confirm('Are you sure to logout?')" class="nav-link">
            <i class="nav-icon far fa-circle text-danger"></i>
            <p class="text">Logout</p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
<!-- /.Main Sidebar Container -->