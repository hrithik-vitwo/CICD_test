<?php
include("../app/v1/connection-company-admin.php");

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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.6/animate.min.css">
    <link rel="stylesheet" href="../public/assets/blobz/blobz.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/css/swiper.min.css">

    <!-- jQuery -->
    <script src="../public/assets/plugins/jquery/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/js/swiper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.9/lottie.min.js"></script>
</head>

<style>
    html {
        height: 100% !important;
        scroll-behavior: smooth;
    }

    body.login-page {
        font-family: 'Poppins', sans-serif;
        background: #fff;
        overflow: hidden;
    }

    section.login-screen {
        width: 100%;
        height: 100vh;
        display: grid;
        place-items: center;
        overflow: hidden;
        /* background-image: url(../../public/assets/gif/login-bg.webm); */
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    section.login-screen video {
        position: fixed;
        top: 0;
        left: 0;
        min-width: 100%;
        min-height: 100%;
        z-index: -100;
        filter: brightness(1.3);
    }

    section.login-screen .container-fluid {
        height: 100%;
    }

    section.login-screen .row {
        align-items: center;
        height: 100%;
        max-width: 90%;
        margin: auto;
    }

    /* .left-vector-bg {
            height: 95vh;
            max-width: 100%;
            background: #003060;
            margin: auto;
            border-radius: 15px;
            padding: 0 2.5rem;
            overflow: hidden;
            position: relative;
            left: 6rem;
        } */


    .left-vector-bg {
        height: 100%;
        max-width: 100%;
        background: #ffffffc4;
        backdrop-filter: blur(6px) brightness(0.8);
        border: 2px solid #00306040;
        margin: 0 auto;
        border-radius: 12px 0 0 12px;
        padding: 3rem 2.5rem;
        overflow: hidden;
        position: relative;
        left: 0;
    }

    .left-vector-bg h2 {
        color: #003060;
        font-weight: 800;
        font-size: clamp(1.5vw, 100%, 2rem);
        line-height: 1.5;
        padding: 0;
    }

    .left-vector-bg .container {
        height: 100%;
        display: grid;
        gap: 1.5rem;
    }

    .viit-desc {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        position: relative;
        top: 0;
    }

    .viit-desc img {
        filter: drop-shadow(8px 14px 17px #fff6);
    }

    .modules-slider {
        height: clamp(13rem, 100%, 5vh);
        border-radius: 15px;
        background: #fff;
        margin-top: 0;
        border: 1px solid #00306040;
    }

    .left-vector-bg .swiper-container hr {
        opacity: 1;
        height: auto;
        border: 1px dotted #ccc;
        display: none;
    }

    .viit-desc p {
        color: #000000;
        font-size: clamp(0.7vw, 50%, 2rem);
        line-height: 1.8;
    }

    .left-vector-bg .swiper-container {
        width: 100%;
        height: 100%;
        border-radius: 12px;
        display: flex;
        align-items: center;
    }

    .left-vector-bg .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

    .left-vector-bg .swiper-wrapper {
        height: 10rem;
        transition-delay: 3000ms !important;
        transition-duration: 300ms !important;
    }

    .form-login input,
    input:-internal-autofill-selected {
        border: 1px solid #fff;
        border-radius: 2rem;
        height: 3rem;
        padding: 0 1rem;
        font-size: 0.80rem;
        font-weight: 400;
        color: #fff;
        background-color: #003060 !important;
    }

    input:-internal-autofill-selected {
        background-color: #003060 !important;
    }

    .form-login button {
        height: 3rem;
        font-size: 0.83rem;
    }

    /* .left-vector-bg .swiper-pagination {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .left-vector-bg .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            background-color: #ddd;
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
            opacity: 0.7;
            transition: background-color 0.3s;
        }

        .left-vector-bg .swiper-pagination-bullet-active {
            background-color: #333;
        } */

    .module-body {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.5rem;
        height: auto;
        border-right: 1px dotted #ccc;
    }

    .module-body span {
        font-weight: 600;
        font-size: clamp(1vw, 100%, 1rem);
    }

    .module-body p {
        font-size: clamp(10px, 40%, 2rem);
        ;
    }

    .logo-section {
        padding: 0;
    }

    .section {
        margin: 0 auto;
        padding: 4rem 0 2rem;
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

    .form-section .form-control {
        position: relative;
        padding: 0.375rem 0rem;
        height: 3rem;
        margin: 3.5rem 0;
        border-bottom: 1px solid var(--color-grays) !important;
        border: 0;
        border-radius: 0;
        background: transparent;
    }

    .form-login .form-label {
        position: relative;
        font-family: inherit;
        font-size: 0.83rem;
        font-weight: normal !important;
        line-height: 1.5;
        top: 0;
        width: 100%;
        color: #fff;
        transition: all 0.2s ease;
    }


    .form-input {
        position: relative;
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
        background: #fff;
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



    .logo-section img {
        width: clamp(4rem, 100%, 8rem);
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
        color: #fff;
        line-height: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
        justify-content: space-between;
    }

    .eye-pass {
        position: absolute;
        bottom: -19px;
        right: 26px;
        z-index: 99;
        color: #fff;
    }

    .form-login {
        display: grid;
        max-width: 100%;
        height: 100%;
        margin: auto;
        background: #003060;
        padding: 2rem 3rem 4rem;
        border-radius: 0 12px 12px 0;
        backdrop-filter: blur(14px) brightness(0.8);
    }

    .form-section {
        margin-top: 0;
        padding: 0 4rem;
    }

    .button-section {
        margin: 0 4rem;
        float: right;
    }

    .button-section button {
        display: flex;
        padding: 1.1rem 2.5rem 1rem 2.5rem;
        border-radius: 4rem;
        background-color: var(--primary);
        transition: all 0.5s ease;
        justify-content: center;
        align-items: center;
        gap: 1em;
        border: none;

        font-size: 18px;
    }

    .button-section button .icon {
        height: 2rem;

        display: flex;
        justify-content: center;
        align-items: center;
    }

    .button-section button .icon span {
        background: white;
        color: #003060;
        border-radius: 100%;
        padding: 0rem;

        width: 0rem;
        height: 0rem;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        transform: rotate(-180deg);

        transition: all 0.5s ease;
    }

    .button-section button:hover .icon span {
        transform: rotate(0deg);
        border-radius: 100%;
        padding: 0.6rem;
        width: 2rem;
        height: 2rem;
    }


    .auth-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        margin-top: 0;
        margin-bottom: 0;
        margin: 0 auto;
        position: relative;
        top: -15px;
        font-weight: 500;
    }

    .auth-section a {
        color: #fff;
    }

    /* .button-section button {
        border: 1px solid #fff;
    }

    .button-section button:hover {
        border-color: #fff;
        background-color: #fff !important;
        color: #003060;
        font-weight: 600;
    } */

    .remember-section {
        color: #fff;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .button-section button {
        width: 10rem;
        background: #ffffff;
        color: #003060;
        font-weight: 600;
        font-size: 0.9rem;
        justify-content: space-between;
        float: right;
    }

    .button-section button:hover {
        border: 1px solid #fff;
        padding: 0 1rem;
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

    .redirect-to-branch {
        position: absolute;
        top: 35px;
        right: 35px;
    }

    .branch-login {
        background: #ffffff;
        color: #003060;
        font-weight: 600;
        font-size: 12px;
        padding: 0.8rem 1.5rem;
        border: 1px solid #ffffff6e;
    }

    .branch-login:hover {
        background: #003060;
        border-color: #fff;
    }

    .branch-login:focus {
        background: #003060;
    }

    .left-col {
        padding-right: 0;
        height: 80%;
    }

    .right-col {
        padding-left: 0;
        height: 80%;
    }

    .welcome-section h2 p {
        font-size: 0.9rem;
        margin-left: 5px;
        font-weight: 200;
    }

    .welcome-section h2 p b {
        font-size: 1.3rem;
        padding-left: 3px;
        font-weight: 600;
    }

    @media(max-width: 768px) {
        .left-vector-bg {
            padding: 1rem;
            border-radius: 0 0 1rem 1rem;
            max-width: 100%;
            left: 0;
            margin-top: 0;
            height: 633px;
        }


        section.login-screen video {
            position: fixed;
            top: 0;
            left: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -100;
            filter: brightness(1.5);
        }

        section.login-screen {
            overflow: auto;
        }

        .left-col {
            padding-right: 7.5px;
            order: 2;
        }

        .right-col {
            padding-left: 7.5px;
        }

    }


    @media (max-width: 575px) {

        .left-vector-bg {
            padding: 1rem;
            border-radius: 0 0 1rem 1rem;
            max-width: 100%;
            left: 0;
            margin-top: 0;
            height: 633px;
        }


        section.login-screen .row {
            align-items: baseline;
            max-width: 100%;
            height: auto;
            margin: 3rem 0;
        }

        body.login-page {
            justify-content: start;
        }

        .form-section {
            margin: 5rem 0;
            padding: 0;
        }

        .welcome-section {
            margin: 55px 0;
        }

        /* .login-screen {
                margin-bottom: 20px;
            } */

        .logo-section {

            display: flex;
            justify-content: start;

        }

        .form-login {
            max-width: 100%;
            height: 600px;
            display: block;
            margin: auto;
            background: #003060;
            padding: 2rem 3rem 4rem;
            border-radius: 1rem 1rem 0 0;
            backdrop-filter: blur(7px) brightness(0.8);
        }

        .button-section {
            position: relative;
            top: -2rem;
            display: flex;
            justify-content: center;
            width: 100%;
            float: none;
            margin: 30px 0;
        }

        .logo-section img {
            max-width: 100px;
        }

        .welcome-section h2 {
            color: #fff;
            font-size: 25px;
            position: relative;
            top: 40px;
            padding-bottom: 1.5rem;
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



<?php

if (isset($_GET["logout"])) {
    unset($_SESSION["logedCompanyAdminInfo"]);
    session_destroy();
    redirect(COMPANY_URL . "login.php");
}

if (isset($_SESSION["logedCompanyAdminInfo"])) {
    redirect(COMPANY_URL);
}

if (isset($_POST["signInBtnSbmt"])) {
    $loginObj = loginAdministratorUser($_POST);
    swalToast($loginObj["status"], $loginObj["message"]);
    if ($loginObj["status"] == "success") {
        redirect(COMPANY_URL);
    }
}

?>

<body class="hold-transition login-page">
    <section class="login-screen">
        <video autoplay muted loop>
            <source src="../../public/assets/gif/login-bg.webm" type="video/mp4">
        </video>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-12 left-col wow bounceInLeft">
                    <div class="left-vector-bg">
                        <div class="container">
                            <div class="logo-section">
                                <img src="../public/main-assets/images/logo/Vitwo-AI-LOGO.png" alt="">
                            </div>
                            <h2>
                                Experience the Power of Intelligence & Automation
                            </h2>
                            <div class="viit-desc">
                                <img src="../../public/assets/img/vitt/vittnew2.png" width="50" alt="">
                                <p>
                                    ViTWO ai transforms your business, thereby optimizing operations & resources, enhancing productivity, and holistic value creation.
                                </p>
                            </div>
                            <div class="modules-slider">
                                <div class="container">
                                    <!-- Slider main container -->
                                    <div class="swiper-container">
                                        <!-- Additional required wrapper -->
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <div class="module-body">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/sidebar/sales-and-distribution.png" width="30" alt="">
                                                    </div>
                                                    <span class="title">
                                                        Sales & Distribution
                                                    </span>
                                                    <p>
                                                        ViTWO ai provides a systematic and unified approach to manage sales and distribution
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="swiper-slide">
                                                <div class="module-body">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/sidebar/material-management.png" width="30" alt="">
                                                    </div>
                                                    <span class="title">
                                                        Material Master
                                                    </span>
                                                    <p>
                                                        In response to evolving customer demands, ViTWO ai recognises the paramount importance of organisational efficiency
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="swiper-slide">
                                                <div class="module-body">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/sidebar/vendor-management.png" width="30" alt="">
                                                    </div>
                                                    <span class="title">
                                                        Vendor Management
                                                    </span>
                                                    <p>
                                                        Streamline vendor relationships and optimise procurement processes with our cutting-edge Vendor Management module
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="swiper-slide">
                                                <div class="module-body">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/sidebar/product-planning.png" width="30" alt="">
                                                    </div>
                                                    <span class="title">
                                                        Production Planning
                                                    </span>
                                                    <p>
                                                        ViTWO ai OM helps plan production by matching the demand with manufacturing capacity, creating schedules based on sales plans
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="swiper-slide">
                                                <div class="module-body">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/sidebar/account-and-finance.png" width="30" alt="">
                                                    </div>
                                                    <span class="title">
                                                        Accounting
                                                    </span>
                                                    <p>
                                                        ViTWO ai covers the entire gamut of record-to-report process to manage accounts payable and receivable, general ledger, budgeting, and financial reporting
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="swiper-slide">
                                                <div class="module-body">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/sidebar/wms.png" width="30" alt="">
                                                    </div>
                                                    <span class="title">
                                                        Warehouse Management
                                                    </span>
                                                    <p>
                                                        Experience the power of centralised control with ViTWO ai’s Storage Module.
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="swiper-slide">
                                                <div class="module-body">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/sidebar/acc-report.png" width="30" alt="">
                                                    </div>
                                                    <span class="title">
                                                        Analytics
                                                    </span>
                                                    <p>
                                                        In the competitive landscape, ViTWO ai emerges as the catalyst for informed decisions
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>

                                        <div class="swiper-pagination"></div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-12 right-col wow bounceInRight">
                    <form class="form-login" action="" method="post" onsubmit="return validate();">
                        <div class="redirect-to-branch">
                            <a href="<?= BRANCH_URL ?>" class="btn btn-primary branch-login"><i class="fas fa-sign-in-alt mr-2"></i>Login as Branch</a>
                        </div>
                        <div class="welcome-section">
                            <h2>
                                Welcome ,
                                <p>You are logging in to the <b>Admin</b></p>
                            </h2>
                        </div>
                        <div class="form-section">
                            <div class="form-control">
                                <label for="email" class="form-label">User Name</label>
                                <input type="text" name="email" id="email" class="form-input" autofocus="" placeholder="none" required>
                            </div>
                            <span class="my-0" style="position: absolute; top: 175px; color: red; font-size: 12px; " id="email_err"></span>

                            <div class="form-control">
                                <i class="far fa-eye float-right eye-pass" id="togglePassword" style="margin-left: -30px; margin-top: 1em; cursor: pointer;"></i>
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="pass" class="form-input" id="pass" autocomplete="current-password" required>
                            </div>
                            <span class="my-0" style="position: absolute; top: 240px; color: red; font-size: 12px; " id="error_pass"></span>

                            <div class="auth-section">
                                <div class="remember-section">
                                    <input type="checkbox">
                                    Remember me
                                </div>
                                <div class="forgot-pass-section">
                                    <a href="<?= COMPANY_URL; ?>forgotpass.php">Forgot Password</a>
                                </div>
                            </div>

                        </div>
                        <div class="button-section">
                            <!-- <button type="submit" class="btn btn-primary login-btn" name="signInBtnSbmt">Login</button> -->
                            <button type="submit" name="signInBtnSbmt">
                                Login
                                <div class="icon">
                                    <span>
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <img src="<?= BASE_URL ?>/public/storage/logo/<?= getAdministratorSettings("logo"); ?>" alt="Logo" style="max-height: 50px;"><br>
               
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>

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
                    <div class="input-group mb-3">
                        <input type="password" name="pass" id="pass" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <span class="my-0" style="position: absolute; top: 240px; color: red; font-size: 12px; " id="error_pass"></span>
                    <div class="row">
                        <div class="col-8">
                           
                            <p class="mt-2">
                                <a href="<?= COMPANY_URL; ?>forgotpass.php">I forgot my password</a>
                            </p>
                        </div>
                        <div class="col-4">
                            <button type="submit" name="signInBtnSbmt" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>


               
            </div>
        </div>
    </div> -->
    <!-- Bootstrap 4 -->
    <script src="../public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="../public/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../public/assets/AdminLTE/dist/js/adminlte.min.js"></script>
    <script src="../main.js"></script>

    <script>
        $(document).ready(function() {
            // Swiper: Slider
            new Swiper(".swiper-container", {
                // loop: true,
                // nextButton: ".swiper-button-next",
                // prevButton: ".swiper-button-prev",
                slidesPerView: 2,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                spaceBetween: 20,
                autoplay: {
                    delay: 5000000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    1920: {
                        slidesPerView: 2,
                        spaceBetween: 15
                    },
                    1028: {
                        slidesPerView: 2,
                        spaceBetween: 15
                    },
                    480: {
                        slidesPerView: 1,
                        spaceBetween: 10
                    }
                }
            });
        });


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
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#pass');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>