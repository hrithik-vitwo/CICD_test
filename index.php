  <?php
include("app/v1/config.php");
?>
<!DOCTYPE html>
<html lang="en">
 
<head>
  <meta charset="utf-8" />
  <link rel="icon" href="%PUBLIC_URL%/favicon.ico" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-color" content="#000000" />
  <meta name="description" content="Web site created using create-react-app" />
  <link rel="apple-touch-icon" href="%PUBLIC_URL%/logo192.png" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- favicon -->
  <link rel="apple-touch-icon" href="apple-touch-icon.png">
  <link rel="shortcut icon" type="image/x-icon" href="<?= BASE_URL ?>public/main-assets/images/fav.png">
  <!-- Bootstrap v4.4.1 css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/bootstrap.min.css">
  <!-- font-awesome css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/font-awesome.min.css">
  <!-- Uicons Regular Rounded css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/uicons-regular-rounded.css">
  <!-- flaticon css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/fonts/flaticon.css">
  <!-- animate css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/animate.css">
  <!-- slick css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/slick.css">
  <!-- owl.carousel css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/owl.carousel.css">
  <!-- off canvas css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/off-canvas.css">
  <!-- magnific popup css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/magnific-popup.css">
  <!-- Main Menu css -->
  <link rel="stylesheet" href="<?= BASE_URL ?>public/main-assets/css/rsmenu-main.css">
  <!-- spacing css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/rs-spacing.css">
  <!-- style css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>style.css">
  <!-- This stylesheet dynamically changed from style.less -->
  <!-- responsive css -->
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>public/main-assets/css/responsive.css">
  <!--
      manifest.json provides metadata used when your web app is installed on a
      user's mobile device or desktop. See https://developers.google.com/web/fundamentals/web-app-manifest/
    -->

  <link rel="manifest" href="%PUBLIC_URL%/manifest.json" />

  <title>ERP</title>

  <style>
    .main-content {
      overflow: hidden;
      height: 100vh;
    }
  </style>

</head>

<body>

  <!-- <div class="index-page">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <a href="<?= ADMIN_URL ?>">
          <div class="card border-0">
            <div class="card-body">
              <div class="icon">
                <img src="<?= BASE_URL ?>public/assets/img/header-icon/admin.png" alt="company-icon">
              </div>
              Super Admin Panel
            </div>
          </div>
        </a>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-3 col-md-3 col-sm-6">
        <a href="<?= COMPANY_URL ?>">
          <div class="card border-0">
            <div class="card-body">
              <div class="icon">
                <img src="<?= BASE_URL ?>public/assets/img/header-icon/company.png" alt="company-icon">
              </div>
              Company Panel
            </div>
          </div>
        </a>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-6">
        <a href="<?= BRANCH_URL ?>">
          <div class="card border-0">
            <div class="card-body">
              <div class="icon">
                <img src="<?= BASE_URL ?>public/assets/img/header-icon/branch.png" alt="company-icon">
              </div>
              Branch Panel
            </div>
          </div>
        </a>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-6">
        <a href="<?= VENDOR_URL ?>">
          <div class="card border-0">
            <div class="card-body">
              <div class="icon">
                <img src="<?= BASE_URL ?>public/assets/img/header-icon/vendor.png" alt="company-icon">
              </div>
              Vendor Panel
            </div>
          </div>
        </a>
      </div>
      <div class="col-lg-3 col-md-3 col-sm-6">
        <a href="<?= CUSTOMER_URL ?>">
          <div class="card border-0">
            <div class="card-body">
              <div class="icon">
                <img src="<?= BASE_URL ?>public/assets/img/header-icon/customer.png" alt="company-icon">
              </div>
              Customer Panel
            </div>
          </div>
        </a>
      </div>
    </div>



  </div> -->




  <!--Preloader start here-->
  <!-- <div id="pre-load">
        <div id="loader" class="loader">
            <div class="loader-container">
                <div class="loader-icon"><img src="./main-assets/images/fav.png" alt="Swipy Creative Agency Html Template "></div>
            </div>
        </div>
    </div> -->
  <!--Preloader area end here-->

  <!-- Main content Start -->
  <div class="main-content">



    <!-- Banner Start -->
    <div class="rs-banner banner-main-home">
      <div class="container">
        <div class="row">
          <div class="col-lg-7">
            <div class="content-wrap">
              <h1 class="title">Welcome to <img src="<?= BASE_URL ?>public/main-assets/images/logo/Vitwo-AI-LOGO.png" alt="logo"></h1>
              <div class="description">
                <p>
                  Jump into the world of strategic automation and expert foresight as we harmonize collaborative outcomes at every click.
                </p>
              </div>
              <h2 class="login-as-text">Login as</h2>
              <hr class="login-as-hr">
              <ul class="banner-bottom pl-0">
                <!-- <li>
                  <a class="readon started" href="#" title="Chartered Accountant">
                    <div class="icon">
                      <img src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/Company_white.png" alt="company-icon">
                    </div>
                    CA
                  </a>
                </li> -->
                <li>
                  <a class="readon started" href="<?= COMPANY_URL ?>">
                    <div class="icon">
                      <img src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/Company_white.png" alt="company-icon">
                    </div>
                    Super Admin
                  </a>
                </li>
                <li>
                  <a class="readon started" href="<?= BRANCH_URL ?>">
                    <div class="icon">
                      <img src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/Branch_white.png" alt="branch-icon">
                    </div>
                    Branch
                  </a>
                </li>
                <li>
                  <a class="readon started" href="<?= LOCATION_URL ?>">
                    <div class="icon">
                      <img src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/Location_white.png" alt="location-icon">
                    </div>
                    Location
                  </a>
                </li>

              </ul>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="main-img text-right md-text-center">
              <img src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/Website Vitt.png" class="vit-img" alt="Images">
              <div class="main-img-bg1">
                <img src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/background-min.png" alt="Images">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="banner-animate">
        <img class="animation-style one scale" src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/hero-shpae-min.png" alt="Images">
        <img class="animation-style two rotated-style" src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/shape1.png" alt="Images">
        <img class="animation-style three veritcal" src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/shape2.png" alt="Images">
        <img class="animation-style four spine" src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/shape3.png" alt="Images">
        <img class="animation-style five veritcal" src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/shape4.png" alt="Images">
        <img class="animation-style six veritcal" src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/shape5.png" alt="Images">
        <img class="animation-style seven rotated-style" src="<?= BASE_URL ?>public/main-assets/images/banner/main-home/shape6.png" alt="Images">

      </div>
    </div>
    <!-- Banner End -->


  </div>
  <!-- Main content End -->


  <!-- start scrollUp  -->
  <div id="scrollUp">
    <i class="fa fa-angle-up"></i>
  </div>
  <!-- End scrollUp  -->




  <script src="<?= BASE_URL ?>main-assets/js/modernizr-2.8.3.min.js"></script>
  <!-- jquery latest version -->
  <script src="<?= BASE_URL ?>main-assets/js/jquery.min.js"></script>
  <!-- Bootstrap v4.4.1 js -->
  <script src="<?= BASE_URL ?>main-assets/js/bootstrap.min.js"></script>
  <!-- op nav js -->
  <script src="<?= BASE_URL ?>main-assets/js/jquery.nav.js"></script>
  <!-- owl.carousel js -->
  <script src="<?= BASE_URL ?>main-assets/js/owl.carousel.min.js"></script>
  <!-- isotope.pkgd.min js -->
  <script src="<?= BASE_URL ?>main-assets/js/isotope.pkgd.min.js"></script>
  <!-- wow js -->
  <script src="<?= BASE_URL ?>main-assets/js/wow.min.js"></script>
  <!-- Skill bar js -->
  <script src="<?= BASE_URL ?>main-assets/js/skill.bars.jquery.js"></script>
  <!-- imagesloaded js -->
  <script src="<?= BASE_URL ?>main-assets/js/imagesloaded.pkgd.min.js"></script>
  <!-- Slick js -->
  <script src="<?= BASE_URL ?>main-assets/js/slick.min.js"></script>
  <!-- waypoints.min js -->
  <script src="<?= BASE_URL ?>main-assets/js/waypoints.min.js"></script>
  <!-- magnific popup js -->
  <script src="<?= BASE_URL ?>main-assets/js/jquery.magnific-popup.min.js"></script>
  <!-- counterup.min js -->
  <script src="<?= BASE_URL ?>main-assets/js/jquery.counterup.min.js"></script>
  <!-- contact form js -->
  <script src="<?= BASE_URL ?>main-assets/js/contact.form.js"></script>
  <!-- main js -->
  <script src="<?= BASE_URL ?>main-assets/js/main.js"></script>


</body>

</html>