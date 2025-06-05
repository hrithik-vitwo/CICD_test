<?php
include("../app/v1/connection-admin.php");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/public/storage/logo/<?= getAdministratorSettings("favicon"); ?>">
    <title><?= getAdministratorSettings("title"); ?> | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../public/assets/plugins/fontawesome-free/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../public/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../public/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../public/assets/AdminLTE/dist/css/adminlte.min.css">
    <!-- jQuery -->
    <script src="../public/assets/plugins/jquery/jquery.min.js"></script>
</head>


<?php

if (isset($_GET["logout"])) {
    unset($_SESSION["logedAdminInfo"]);
    session_destroy();
    redirect(ADMIN_URL . "login.php");
}

if (isset($_SESSION["logedAdminInfo"])) {
    redirect(ADMIN_URL);
}

if (isset($_POST["signInBtnSbmt"])) {
    $loginObj = ForgotAdministratorUser($_POST);
    swalToast($loginObj["status"], $loginObj["message"],ADMIN_URL);
   
}

?>

<body class="hold-transition dark-mode login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <img src="<?= BASE_URL ?>/public/storage/logo/<?= getAdministratorSettings("logo"); ?>" alt="Logo" style="max-height: 50px;"><br>
                <!-- <a href="" class="h2">
                    <b><?php //echo getAdministratorSettings("title"); 
                        ?></b>
                </a> -->
            </div>
            <div class="card-body">
                <p class="login-box-msg">Forgot Your Password</p>

                <form action="" method="post" onsubmit="return validate();">
                    <div class="input-group mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <span class="my-0" style="position: absolute; top: 175px; color: red; font-size: 12px; " id="email_err"></span>
                   <div class="row">
                        <div class="col-8">
                            <!-- <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">Remember Me</label>
                            </div> -->
                            <p class="mt-2">
                                <a href="<?= ADMIN_URL;?>login.php">Back To Login</a>
                            </p>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" name="signInBtnSbmt" class="btn btn-primary btn-block">Submit</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>


                <!-- <p class="mb-0">
                    <a href="" class="text-center">Register a new membership</a>
                </p> -->
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
    <!-- Bootstrap 4 -->
    <script src="../public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../public/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../public/assets/AdminLTE/dist/js/adminlte.min.js"></script>

    <script src="../main.js"></script>
</body>

</html>