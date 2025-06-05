<?php
require_once("../app/v1/connection-branch-admin.php");
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
// administratorAuth();
?>
<link rel="stylesheet" href="../public/assets/listing.css">

<style>
    .gstPortalAuthStatusIcon {
        font-size: 40px;
        color: #030360;
    }

    /********otp start******/

    .title {
        max-width: 400px;
        margin: auto;
        text-align: center;
        font-family: "Poppins", sans-serif;
    }

    .title h3 {
        font-weight: bold;
    }

    .title p {
        font-size: 12px;
        color: #118a44;
    }

    .title p.msg {
        color: initial;
        text-align: initial;
        font-weight: bold;
    }

    .otp-input-fields {
        margin: auto;
        max-width: 400px;
        width: auto;
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 15px 10px;
    }

    .otp-input-fields input {
        height: 40px;
        width: 40px;
        background-color: transparent;
        border-radius: 4px;
        border: 1px solid #2f8f1f;
        text-align: center;
        outline: none;
        font-size: 16px;
        /* Firefox */
    }

    .otp-input-fields input::-webkit-outer-spin-button,
    .otp-input-fields input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .otp-input-fields input[type=number] {
        -moz-appearance: textfield;
    }

    .otp-input-fields input:focus {
        border-width: 2px;
        border-color: #287a1a;
        font-size: 20px;
    }

    .result {
        max-width: 400px;
        margin: auto;
        padding: 24px;
        text-align: center;
    }

    .result p {
        font-size: 24px;
        font-family: "Antonio", sans-serif;
        opacity: 1;
        transition: color 0.5s ease;
    }

    .result p._ok {
        color: green;
    }

    .result p._notok {
        color: red;
        border-radius: 3px;
    }

    .otp-section {
        margin-top: 39px;
        background: #ebebeb;
        padding: 10px;
        border-radius: 12px;
        box-shadow: 2px 7px 14px -3px #868686;
    }

    :where(.otp-input-fields .otp-input-fields-count-time)>.otp-section {
        height: 180px;
    }

    .otp-input-fields,
    .otp-input-fields-count-time {
        height: 160px;
        padding-top: 4em;
    }

    .second-step {
        display: none;
    }
    .otp-input-fields-count-time {
        display: none;
    }
    .connected-text {
        display: none;
    }
    /* .otp-input-fields-count-time {
        display: none;
    } */


    /********otp end******/
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <?php

    $isGstPortalAuthorized = false;

    ?>
    <section class="content">
        <div class="container-fluid my-4">
            <div class="row ">
                <div class="card">
                    <div class="div w-100 p-2 d-flex justify-content-between">
                        <div class="align-self-center">
                            <p>Manage Compliance</p>
                        </div>
                        <div class="align-self-end">
                            <a style="cursor: pointer;" data-toggle="modal" id="gstPortalAuthorizeBtn" data-target="#tempReconListModal"> <?= ($isGstPortalAuthorized) ? '<i class="fas fa-lock-open gstPortalAuthStatusIcon"></i>' : '<i class="fas fa-lock gstPortalAuthStatusIcon"></i>' ?></a>
                        </div>
                    </div>
                </div>
                <div class="modal fade right customer-modal" id="tempReconListModal" tabindex="-1" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true" role="dialog">
                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                        <!--Content-->
                        <div class="modal-content">
                            <!--Header-->

                            <div class="modal-body">
                                <div class="first-step" id="firstStep">
                                    <lottie-player src="https://assets9.lottiefiles.com/packages/lf20_ofa3xwo7.json" class="ocr-reading-animation" background="transparent" speed="1" style="width: 200px; height: 300px; margin: 0 auto;" loop autoplay>

                                    </lottie-player>

                                    <p class="text-sm text-danger text-center font-bold">Sorry, I am not connected to GST server</p>
                                    <div class="connct-btn-section text-center mt-3 mb-2" id="connectBtn">
                                        <button class="btn btn-primary connect-btn">Connect</button>
                                    </div>
                                </div>

                                <div class="second-step" id="secondStep">
                                    <lottie-player src="https://assets9.lottiefiles.com/packages/lf20_ofa3xwo7.json" class="ocr-reading-animation" background="transparent" speed="1" style="width: 200px; height: 300px; margin: 0 auto;" loop autoplay>
                                    </lottie-player>

                                    <p class="text-sm text-success text-center font-bold connected-text">I am not connected</p>

                                    <form action=" javascript: void(0)" class="otp-form" name="otp-form">
                                        <div class="otp-section">
                                            <div id="otpInputFields">
                                                <div class="title mt-3">
                                                    <p class="msg text-center">Please enter OTP to verify</p>
                                                </div>
                                                <div class="otp-input-fields bg-transparent">
                                                    <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(1)' maxlength=1>
                                                    <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(2)' maxlength=1>
                                                    <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(3)' maxlength=1>
                                                    <input class="otp" type="text" oninput='digitValidate(this)' onkeyup='tabChange(4)' maxlength=1>
                                                </div>
                                            </div>
                                            <div class="otp-input-fields-count-time" id="otpCountTime">
                                                <p class="text-center mt-3 mb-3">05:59</p>
                                            </div>
                                            <div class="verify-btn-section text-center mt-2 mb-2" id="verifyBtn">
                                                <button class="btn btn-primary verify-otp-btn" id="verifyOTP">Verify OTP</button>
                                            </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

        </div>
</div>
<div class="row">

</div>
</div>
</section>
<!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
require_once("common/footer.php");
?>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="../public/assets/piechart/piecore.js"></script>
<script src="//www.amcharts.com/lib/4/charts.js"></script>
<script src="//www.amcharts.com/lib/4/themes/animated.js"></script>
<script src="../public/assets/apexchart/apexcharts.min.js"></script>
<script src="../public/assets/apexchart/chart-data.js"></script>
<script src="../../public/assets/dist/lottie-player.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://www.amcharts.com/lib/3/amcharts.js?x"></script>
<script src="https://www.amcharts.com/lib/3/serial.js?x"></script>
<script src="https://www.amcharts.com/lib/3/themes/dark.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        console.log("JQuery is ready!");
    });


    var otp_inputs = document.querySelectorAll(".otp__digit");
    var mykey = "0123456789".split("");
    otp_inputs.forEach((_) => {
        _.addEventListener("keyup", handle_next_input);
    });

    function handle_next_input(event) {
        let current = event.target;
        let index = parseInt(current.classList[1].split("__")[2]);
        current.value = event.key;

        if (event.keyCode == 8 && index > 1) {
            current.previousElementSibling.focus();
        }
        if (index < 6 && mykey.indexOf("" + event.key + "") != -1) {
            var next = current.nextElementSibling;
            next.focus();
        }
        var _finalKey = "";
        for (let {
                value
            }
            of otp_inputs) {
            _finalKey += value;
        }
        if (_finalKey.length == 6) {
            document.querySelector("#_otp").classList.replace("_notok", "_ok");
            document.querySelector("#_otp").innerText = _finalKey;
        } else {
            document.querySelector("#_otp").classList.replace("_ok", "_notok");
            document.querySelector("#_otp").innerText = _finalKey;
        }
    }


    $(document).ready(function() {
        $("#connectBtn").click(function() {
            $("#firstStep").hide();
            $("#secondStep").show();
        });
        $("#verifyBtn").click(function() {
            $("#otpInputFields").hide();
            $("#verifyOTP").hide();
            $("#otpCountTime").show();
            $(".connected-text").show();
        });
    });



    let digitValidate = function(ele) {
        console.log(ele.value);
        ele.value = ele.value.replace(/[^0-9]/g, '');
    }

    let tabChange = function(val) {
        let ele = document.querySelectorAll('input');
        if (ele[val - 1].value != '') {
            ele[val].focus()
        } else if (ele[val - 1].value == '') {
            ele[val - 2].focus()
        }
    }
</script>