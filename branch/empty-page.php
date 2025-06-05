<?php
require_once("../app/v1/connection-branch-admin.php");
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar-new.php");
//userAuth();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
        <div class="container-fluid">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Empty Page</a></li>
            </ol>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <?php
               
                $menuSubMenuListObjt = getAdministratorMenuSubMenu();
                console($menuSubMenuListObjt);
                
                console($_SESSION);
                
        checkAccess('edit');
               /* $to = $POST["email"];
                $user_name = $row['fldAdminName'];
                $url = BRANCH_URL;
                $user_id = $POST['email'];
                $password = $adminPassword;
                $sub = 'Welcome to VITWO!';
                $msg = 'Welcome <b>' . $user_name . ',</b><br>
                Thank you for subscribing vitwo!<br>
                To get started, here is your login credentials:<br>
                <b>Url:</b> ' . $url . '<br>
                <b>User Id:</b> ' . $user_id . '<br>
                <b>Password:</b> ' . $password . '<br>
                <b>Step 1 :</b> Log in to your account <br> 
                <b>Step 2 :</b> Update your profile data and others information';
                $emailReturn = SendMailByMySMTPmailTemplate($to, $sub, $msg, $tmpId = null);*/
                ?>
            </div>
            <!-- /.row -->
        </div>
    </section>
    <!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
require_once("common/footer.php");
?>