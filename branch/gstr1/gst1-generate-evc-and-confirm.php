<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">


<style>
    section.gstr-1 {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gst-one-filter {
        left: 0;
        top: 0;
    }

    .gst-one-filter a.active {
        background-color: #003060;
        color: #fff;
    }

    .proceedToFile {
        display: grid;
        align-items: center;
        place-content: start;
        gap: 17px;
        padding-left: 21px;
    }

    .otp-popup .proceedToFile {
        display: grid;
        place-content: center;
        padding-left: 0;
    }

    .proceedToFile img {
        max-width: 150px;
        margin: 20px auto;
    }

    .proceedToFile .text {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 10px;
    }

    .evc-selection .box {
        border: 1px solid #929292;
        padding: 25px;
        background: #ccc;
        border-radius: 12px;
        width: 200px;
        cursor: pointer;
    }

    .evc-selection .box.active-box {
        border-color: #003060;
        background: #003060;
    }

    .evc-selection .box p {
        font-size: 12px;
        margin: 7px 0;
        text-align: center;
    }

    .evc-selection .box.active-box p {
        color: #fff;
    }

    .evc-selection .box.active-box:before {
        border-color: #fff;
    }

    .evc-selection .box:before {
        content: "";
        position: relative;
        top: -22px;
        display: inline-block;
        transform: translateX(-14px);
        width: 7px;
        height: 7px;
        border: 2px solid #929292;
        border-radius: 50%;
    }

    .otp-inputs input {
        width: 30px;
        text-align: center;
    }

    .success-popup .modal-body {
        display: grid;
        gap: 0px;
    }

    .success-popup .modal-body img {
        margin: 0 auto;
    }

    .success-popup .modal-body p {
        padding: 0 45px 25px;
    }

    .success-popup .modal-body button {
        width: 70px;
        margin: 0 auto 25px;
        font-size: 15px !important;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= BRANCH_URL ?>gstr1/gst1-report-graphical.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>GSTR1</a></li>
            <li class="breadcrumb-item active"><a href="gst1-generate-evc.php<?= isset($_GET["period"]) ? '?period=' . $_GET["period"] : '' ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Generate EVC</a></li>
            <li class="breadcrumb-item active"><a href="gst1-generate-evc-and-confirm.php<?= isset($_GET["period"]) ? '?period=' . $_GET["period"] : '' ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Conirm EVC <?= $branch_gstin_file_frequency != "" ? "(" . date("F, Y", strtotime($gstr1ReturnPeriod)) . " - " . strtoupper($branch_gstin_file_frequency) . ")" : "" ?></a></li>
            <li class="back-button">
                <a href="gst1-report-concised.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
        <!-- <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1</h4> -->
        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="./gst1-preview.php<?= isset($_GET["period"]) ? '?period=' . $_GET["period"] : '' ?>" class="btn"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <a href="" class="btn active"><i class="fa fa-list mr-2"></i>Pending Filling</a>
            </div>
        </div>

        <div class="card bg-light">
            <div class="card-header p-3 rounded-top">
                <h3 class="text-sm text-white mb-0 pl-3">Pending Filling</h3>
            </div>
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-8 col-sm-8 col-sm-8">
                        <h4 class="text-sm font-bold m-4">
                            Generate EVC
                        </h4>
                        <!-- <div class="proceedToFile h-50">
                            <p class="text-sm">Select Any one</p>
                            <div class="evc-selection d-flex gap-2">
                                <div class="box">
                                    <p>Ritesh Saha</p>
                                    <p>ADDR34536TY8</p>
                                </div>
                                <div class="box">
                                    <p>Ritesh Saha</p>
                                    <p>ADDR34536TY8</p>
                                </div>
                                <div class="box active-box">
                                    <p>Ritesh Saha</p>
                                    <p>ADDR34536TY8</p>
                                </div>
                            </div>
                        </div> -->
                        <button class="btn btn-primary float-right" data-toggle="modal" data-target="#oTpPopup">Generate EVC</button>
                        <div id="" class="row p-0 m-0 text-center">
                            <div class="proceedToFile text-center">
                                <img src="../../public/assets/img/password-otp.png" class="my-0" width="75" alt="">
                                <div class="text">
                                    <p class="text-sm">Please enter the otp to complete the filling process</p>
                                    <div id="OtpInputs" class="otp-inputs d-flex gap-2 my-3">
                                        <input class="input form-control p-2" type="text" inputmode="numeric" maxlength="1" />
                                        <input class="input form-control p-2" type="text" inputmode="numeric" maxlength="1" />
                                        <input class="input form-control p-2" type="text" inputmode="numeric" maxlength="1" />
                                        <input class="input form-control p-2" type="text" inputmode="numeric" maxlength="1" />
                                        <input class="input form-control p-2" type="text" inputmode="numeric" maxlength="1" />
                                        <input class="input form-control p-2" type="text" inputmode="numeric" maxlength="1" />
                                    </div>
                                    <script>
                                        // script.js
                                        const inputs = document.getElementById("OtpInputs");
                                        inputs.addEventListener("input", function(e) {
                                            const target = e.target;
                                            const val = target.value;
                                            if (isNaN(val)) {
                                                target.value = "";
                                                return;
                                            }
                                            if (val != "") {
                                                const next = target.nextElementSibling;
                                                if (next) {
                                                    next.focus();
                                                }
                                            }
                                        });
                                        inputs.addEventListener("keyup", function(e) {
                                            const target = e.target;
                                            const key = e.key.toLowerCase();
                                            if (key == "backspace" || key == "delete") {
                                                target.value = "";
                                                const prev = target.previousElementSibling;
                                                if (prev) {
                                                    prev.focus();
                                                }
                                                return;
                                            }
                                        });
                                    </script>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#successPopup">Submit</button>
                                    <div class="modal success-popup fade" id="successPopup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <img src="../../public/assets/img/correct 2.jpg" alt="">
                                                    <p class="text-lg text-center font-bold">Congratulations</p>
                                                    <p class="text-center">Your EVC is successful created. It has generated the below acknowledgment as a confirmation of your return submission</p>
                                                    <button class="btn btn-primary" data-dismiss="modal">Ok</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-sm-4">
                        <div class="card w-75 ml-auto timeline-card mb-0">
                            <div class="card-body">
                                <div id="content">
                                    <ul class="timeline">
                                        <li class="event progress-success">
                                            <h3>Initiation</h3>
                                            <!-- <p>Mr.Guria</p>
                                            <p>16-08-2023</p> -->
                                        </li>
                                        <li class="event progress-success">
                                            <h3>Connect</h3>
                                            <!-- <p>Mr.Guria</p>
                                            <p>16-08-2023</p> -->
                                        </li>
                                        <li class="event progress-success border-color-light">
                                            <h3>Save File</h3>
                                            <!-- <p>Mr.Guria</p>
                                            <p>16-08-2023</p> -->
                                        </li>
                                        <li class="event progress-disable">
                                            <h3>Generate EVC</h3>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?php
require_once("../common/footer.php");
?>