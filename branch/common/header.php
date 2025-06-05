
<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  
  
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>public/storage/logo/<?= getAdministratorSettings("favicon"); ?>">
  
  <title><?= getAdministratorSettings("title") ?> | Dashboard</title>

  
  
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/jqvmap/jqvmap.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/jqvmap/select2.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/AdminLTE/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/summernote/summernote-bs4.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/plugins/bs-stepper/css/bs-stepper.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/sass/custom.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/ref-style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <!-- <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100;1,200;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet"> -->

        
  <style>
    .btn-primary {
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card-primary:not(.card-outline)>.card-header {
      background-color: #003060 !important;
    }

    .btn-primary,
    .page-item.active .page-link {
      background-color: #003060 !important;
      border-color: #003060 !important;
    }

    .btn-primary .fa-plus {
      margin-right: 5px;
    }

    .menu-btn img,
    .rounded .nav-icon {
      display: none;
    }

    .menu-btn .fa-edit {
      margin-left: 10px;
    }

    .btn-outline-primary {
      color: #003060;
      border-color: #003060;
    }

    .menu-btn {
      margin-bottom: 10px;
    }

    .btn-outline-primary:hover {
      background-color: #003060 !important;
      border-color: #003060;
      color: #fff;
    }

    .btn-outline-primary:hover label {
      color: #fff;
    }

    .form-table {
      width: 100%;
    }

    #customFields .btnstyle {
      position: absolute;
      right: 20px;
      top: 20px;
    }

    .step2 {
      position: relative;
    }

    .pagination {
      width: 100%;
      display: flex;
      justify-content: end;
      padding: 13px 20px;
      border-top: 1px solid #dee2e6;
      border-radius: 0;
    }

    .pagination a.number.current {
      background: #003060;
      color: #fff;
    }

    .pagination a {
      font-size: 14px;
      color: black;
      float: left;
      padding: 8px 10px;
      text-decoration: none;
      border: 1px solid rgba(0, 0, 0, 0.5);
    }

    .pagination a.active {
      background-color: #000;
      color: #fff;
    }

    .pagination a:hover:not(.active) {
      background-color: #003060;
      color: #fff;
      border-color: #fff;
    }

    #mytable_paginate,
    #mytable_info {
      display: none;
    }

    /*************loader************/

    /* .preloader-logo {
      background: #fff;
      height: 100%;
    } */

    /* Initial state of the preloader */
    .preloader {
      opacity: 1;
      transition: opacity 50s ease-in;
    }

    /* Animation to fade out the preloader */
    @keyframes fadeOut {
      0% {
        opacity: 1;
      }

      100% {
        opacity: 0;
      }
    }

    /* Apply the fade out animation to the preloader */
    .fade-out {
      animation: fadeOut 0.2s ease-in forwards;
    }

    .preloader-logo {
      background: #fff;
      height: 100%;
    }

    .preloader-logo img.vitwo-loader {
      width: 100%;
      height: auto;
      max-width: 350px;
      object-fit: contain;
    }
  </style>
  <!-- jQuery -->
  <script src="<?= BASE_URL ?>public/assets/plugins/jquery/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
</head>
<body class="sidebar-mini layout-fixed sidebar-collapse">
  <div class="wrapper">
    <!-- Preloader -->
    <div id="preloader" class="preloader flex-column justify-content-center align-items-center preloader-logo">
      <img class="vitwo-loader" src="<?= BASE_URL ?>public/assets/gif/AI-Loader.gif" alt="loading...">
    </div>
  </div>
  <script>
    new WOW().init();
    // Get the preloader element
    const preloader = document.getElementById('preloader');
    // Listen for the "load" event on the window
    window.addEventListener('load', () => {
      // Add the fade-out class to initiate the fade out animation
      preloader.classList.add('fade-out');
      // Remove the preloader from the DOM after the animation finishes
      preloader.addEventListener('animationend', () => {
        preloader.remove();
      });
    });
  </script>
</body>