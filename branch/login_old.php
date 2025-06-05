<?php
require_once("../app/v1/connection-branch-admin.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/public/storage/logo/<?= getAdministratorSettings("favicon"); ?>">
    <title><?= getAdministratorSettings("title"); ?> | Log in</title>

    <!-- Google Font: Source Sans Pro -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../public/assets/plugins/fontawesome-free/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="../public/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../public/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../public/assets/AdminLTE/dist/css/adminlte.min.css">
    <!-- <link rel="stylesheet" href="../../public/assets/listing.css"> -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.6/animate.min.css">
    <link rel="stylesheet" href="../public/assets/blobz/blobz.min.css">


    <!-- jQuery -->
    <script src="../public/assets/plugins/jquery/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.9/lottie.min.js"></script>





    <style>
        html {
            height: 100% !important;
            scroll-behavior: smooth;
        }

        body.login-page {
            font-family: 'Poppins', sans-serif;
            background: #fff;
        }

        .section.login-screen .row {
            overflow: hidden;
        }

        .section {
            margin: 0 auto;
            padding: 4rem 0 2rem;
        }

        .container {
            max-width: 75rem;
            height: auto;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        .centered {
            text-align: center;
            vertical-align: middle;
            margin-bottom: 1rem;
        }

        .heading {
            font-family: inherit;
            font-size: 1.75rem;
            font-weight: 600;
            line-height: 1.25;
            color: var(--color-black);
            text-transform: uppercase;
        }

        .btn {
            display: inline-block;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            outline: none;
            border: none;
            border-radius: 0.25rem;
            text-transform: unset;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease-in-out;
        }

        .btn-darken {
            padding: 0.625rem 1.5rem;
            color: var(--color-white);
            background: var(--color-black);
            box-shadow: var(--shadow-medium);
        }

        .form-control {
            position: relative;
            height: 3rem;
            margin: 3.5rem 0;
            border-bottom: 1px solid var(--color-grays) !important;
            border: 0;
            border-radius: 0;
        }

        .form-label {
            position: absolute;
            font-family: inherit;
            font-size: 0.93rem;
            font-weight: 400;
            line-height: 1.5;
            top: 0.5rem;
            width: 100%;
            color: #9e9c9c;
            transition: all 0.2s ease;
        }

        .form-input {
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
            width: 100%;
            height: 100%;
            padding: 1rem 1rem 1rem 0;
            resize: none;
            border: none;
            outline: none;
            color: #8d8888;
            background: transparent;
            transition: all 0.2s ease;
            border-bottom: 1px solid #c9c5c5;
        }


        .form-input::-moz-placeholder {
            opacity: 0;
            visibility: hidden;
            color: transparent;
        }

        .form-input:-ms-input-placeholder {
            opacity: 0;
            visibility: hidden;
            color: transparent;
        }

        .form-input::placeholder {
            opacity: 0;
            visibility: hidden;
            color: transparent;
        }

        .form-input:not(:-moz-placeholder-shown).form-input:not(:focus)~.form-label {
            top: -0.75rem;
            left: 0;
            font-size: 0.875rem;
            z-index: 9;
            -moz-transition: all 0.2s ease;
            transition: all 0.2s ease;
        }

        .form-input:not(:-ms-input-placeholder).form-input:not(:focus)~.form-label {
            top: -0.75rem;
            left: 0;
            font-size: 0.875rem;
            z-index: 9;
            -ms-transition: all 0.2s ease;
            transition: all 0.2s ease;
        }

        .form-input:focus~.form-label,
        .form-input:not(:placeholder-shown).form-input:not(:focus)~.form-label {
            top: -0.75rem;
            left: 0;
            font-size: 0.875rem;
            z-index: 9;
            transition: all 0.2s ease;
        }

        .form-areas {
            resize: none;
            max-width: 100%;
            min-height: 7rem;
            max-height: 16rem;
        }

        .contact-column {
            max-width: 30rem;
            width: 100%;
            height: auto;
            margin: 0 auto;
        }

        .contact-inform {
            padding: 4rem 2rem 2rem;
            border-radius: 0.25rem;
            color: var(--color-black);
            background: var(--color-white);
            box-shadow: var(--shadow-large);
        }


        .logo-section {
            margin: 20px 0;
        }

        .logo-section img {
            max-width: 150px;
            width: 100%;
            height: 50px;
            object-fit: contain;
        }

        .slogan-text {
            margin: 20px 0;
            position: relative;
            top: -40px;
            text-align: left;
        }

        .slogan-text h4 {
            font-weight: 600;
            line-height: 2.3rem;
        }

        .slogan-text hr {
            border-top: 2px solid #000;
            width: 85px;
            margin: 20px 0;
        }

        .welcome-section {
            margin: 25px 0;
        }

        .welcome-section h2 {
            font-weight: 800;
            font-size: 35px;
            color: #0054a7;
            line-height: 1.5rem;
        }

        .eye-pass {
            position: relative;
            z-index: 99;
        }

        .form-login {
            max-width: 500px;
        }

        .auth-section {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-top: -40px;
            margin-bottom: 45px;
        }

        .remember-section {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .button-section button {
            width: 100%;
            background: #003060;
        }

        .button-section button:hover {
            width: 100%;
            background: #003060;
        }

        .para-section {
            margin: 25px 0;
            font-size: 11px;
        }

        .vector-img-section img {
            width: 100%;
            height: 300px;
            object-fit: contain;
        }

        .redirect-to-company {
            position: absolute;
            top: 35px;
            right: 35px;
        }

        .company-login {
            background: #003060;
            font-size: 12px;
        }
        .company-login:hover {
            background: #003060;
        }
        .company-login:focus {
            background: #003060;
        }


        @media (max-width: 575px) {

            body.login-page {
                justify-content: start;
            }

            /* .login-screen {
                margin-bottom: 20px;
            } */

            .logo-section {

                display: flex;
                justify-content: start;

            }

            .logo-section img {
                max-width: 100px;
            }

            .welcome-section h2 {
                font-size: 25px;
            }

            .vector-img-section {
                display: flex;
                justify-content: center;
            }

            .slogan-text {
                display: none;
            }


        }
    </style>

</head>


<?php

if (isset($_GET["logout"])) {
    unset($_SESSION["logedBranchAdminInfo"]);
    session_destroy();
    redirect(BRANCH_URL . "login.php");
}
if (isset($_GET["logoutCompanyFromBranch"])) {
    $loginObj = VisitBranchesUserLogout($_POST);
    if ($loginObj["status"] == "success") {
        redirect(COMPANY_URL . "manage-branches.php");
    } else {
        swalToast($loginObj["status"], $loginObj["message"]);
    }
}
if (isset($_GET["logoutBranchFromLocation"])) {
    $loginObj = VisitLocationsUserLogout($_POST);
    if ($loginObj["status"] == "success") {
        redirect(BRANCH_URL . "manage-locations.php");
    } else {
        swalToast($loginObj["status"], $loginObj["message"]);
    }
}

if (isset($_SESSION["logedBranchAdminInfo"])) {
    redirect(BRANCH_URL);
}

if (isset($_POST["signInBtnSbmt"])) {
    $loginObj = loginAdministratorUser($_POST);
    if ($loginObj["status"] == "success") {
        if ($_SESSION["logedBranchAdminInfo"]["adminType"] == 'location') {
            redirect(LOCATION_URL);
        } else {
            redirect(BRANCH_URL);
        }
    } else {
        swalToast($loginObj["status"], $loginObj["message"]);
    }
}

?>

<body class="hold-transition login-page">

    <div class="redirect-to-company">
        <a href="<?= COMPANY_URL ?>" class="btn btn-primary company-login"><i class="fas fa-sign-in-alt mr-2"></i>Login as Company</a>
    </div>

    <section class="login-screen">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 wow bounceInLeft">
                    <div class="logo-section">
                        <img src="../public/assets/img/logo/vitwo-logo.png" alt="">
                    </div>
                    <div class="vector-img-section">
                        <img src="../public/assets/gif/animation_640_lf2gpcp2.gif" alt="">
                    </div>
                    <div class="slogan-text">
                        <h4 class="text-sm">Powered by <a href="http://devalpha.vitwo.ai/">Vitwo</a> </h4>
                    </div>
                    <!-- <div class="tk-blob" style="--time: 20s; --amount: 5; fill: #56cbb9;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 747.2 726.7">
                            <path d="M539.8 137.6c98.3 69 183.5 124 203 198.4 19.3 74.4-27.1 168.2-93.8 245-66.8 76.8-153.8 136.6-254.2 144.9-100.6 8.2-214.7-35.1-292.7-122.5S-18.1 384.1 7.4 259.8C33 135.6 126.3 19 228.5 2.2c102.1-16.8 213.2 66.3 311.3 135.4z"></path>
                        </svg> -->
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-12 wow bounceInRight">
                    <form class="form-login" action="" method="post" onsubmit="return validate();">
                        <div class="welcome-section">
                            <h2>
                                Welcome
                            </h2>
                        </div>
                        <div class="form-section">
                            <div class="form-control">
                                <input type="text" name="email" id="email" class="form-input" autofocus="" placeholder="none" required>
                                <label for="email" class="form-label">User Name</label>
                            </div>
                            <span class="my-0" style="position: absolute; top: 175px; color: red; font-size: 12px; " id="email_err"></span>

                            <div class="form-control">
                                <i class="far fa-eye float-right eye-pass" id="togglePassword" style="margin-left: -30px; margin-top: 1em; cursor: pointer;"></i>
                                <input type="password" name="pass" class="form-input" id="pass" autocomplete="current-password" required>
                                <label for="password" class="form-label">Password</label>
                            </div>
                            <span class="my-0" style="position: absolute; top: 240px; color: red; font-size: 12px;" id="error_pass"></span>

                            <div class="auth-section">
                                <div class="remember-section">
                                    <input type="checkbox">
                                    Remember me
                                </div>
                                <div class="forgot-pass-section">
                                    <a href="">Forgot Password</a>
                                </div>
                            </div>

                        </div>
                        <div class="button-section">
                            <button type="submit" class="btn btn-primary login-btn" name="signInBtnSbmt">Login</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>





    <!-- /.login-box -->
    <!-- Bootstrap 4 -->
    <script src="../public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../public/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../public/assets/AdminLTE/dist/js/adminlte.min.js"></script>

    <script>
        function validate() {
            var email = $("#email").val();
            var pass = $("#pass").val();
            var vendor_code = $("#vendor_code").val();
            var customer_code = $("#customer_code").val();

            //  var email_regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            //  var password_regex1=/([a-z].*[A-Z])|([A-Z].*[a-z])([0-9])+([!,%,&,@,#,$,^,*,?,_,~])/;
            //  var password_regex2 = /([0-9])/;
            //  var password_regex3=/([!,%,&,@,#,$,^,*,?,_,~])/;

            if (pass == '') {
                $("#error_pass").show();
                $("#error_pass").html("Please Enter Strong Password");
                $("#error_pass").focus();
                return false;
            } else {
                $("#error_pass").hide();
            }

            if (email == '') {
                $("#email_err").show();
                $("#email_err").html("Please Enter User Name");
                $("#email").focus();
                return false;
            } else {
                $("#email_err").hide();
            }

            $(".btn").toggleClass("disabled");
            $(".btn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');


        }
    </script>

    <script>
        $(document).ready(function() {
            console.log("Ready to work");
            $.ajax({
                url: 'http://ocrserver.centralindia.cloudapp.azure.com:8000/api/v1/ocr/azure/',
                cache: false,
                contentType: false,
                processData: false,
                type: 'get',
                success: function(responseData) {
                    console.log(responseData);
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                    console.log(status);
                    console.log(error);
                }
            });


            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#pass');

            togglePassword.addEventListener('click', function(e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                // toggle the eye slash icon
                this.classList.toggle('fa-eye-slash');
            });

            new WOW().init();

        });
    </script>



</body>

</html>