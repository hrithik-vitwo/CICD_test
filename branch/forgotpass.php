<?php
require_once("../app/v1/connection-branch-admin.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?=BASE_URL?>/public/storage/logo/<?= getAdministratorSettings("favicon"); ?>">
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
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>

</head>


<?php

if(isset($_GET["logout"])){
    unset($_SESSION["logedBranchAdminInfo"]);
    session_destroy();
    redirect(BRANCH_URL."login.php");
}
if(isset($_GET["logoutAdminFromBranch"])){
    unset($_SESSION["logedBranchAdminInfo"]);
    redirect(BRANCH_URL."manage-branches.php");
}

if(isset($_SESSION["logedBranchAdminInfo"])){
    redirect(BRANCH_URL);
}

if(isset($_POST["signInBtnSbmt"])){
    $loginObj=ForgotAdministratorUser($_POST);
    if($loginObj["status"]=="success"){
     swalToast($loginObj["status"], $loginObj["message"],BRANCH_URL);
    }else{
     swalToast($loginObj["status"], $loginObj["message"]);
    }
}

?>

<body class="hold-transition dark-mode login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <img src="<?=BASE_URL?>/public/storage/logo/<?= getAdministratorSettings("logo"); ?>" alt="Logo" style="max-height: 50px;"><br>   
                <!-- <a href="" class="h2">
                    <b><?php //echo getAdministratorSettings("title"); ?></b>
                </a> -->
            </div>
            <div class="card-body">
                <p class="login-box-msg">Forgot Your Password</p>
                <form action="" method="post" onsubmit="return validate();">

                    <div class="input-group mb-4">

                        <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div> 

                    </div>
                    <div class="row">
                        <div class="col-8">
                            <!-- <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">Remember Me</label>
                            </div> -->
                            <p class="mt-2">
                                <a href="<?= BRANCH_URL;?>login.php">Back To Login</a>
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