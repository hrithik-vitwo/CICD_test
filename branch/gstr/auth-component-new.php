<?php

// require_once("../../app/v1/connection-branch-admin.php");
// require_once("../../app/v1/functions/branch/func-compliance-controller.php");

$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
// $authGstinPortalObj->refreshToken()
if ($authObj["status"] == "success") {
?>
    <div id="complianceAuthDiv" class="row p-0 m-0 text-center">
        <div class="proceedToFile">
            <div class="text">
                <p class="text-sm text-success text-center"><?= $authObj["message"]; ?></p>
            </div>
        </div>

    </div>
<?php
} else {
?>
    <div id="complianceAuthDiv" class="row p-0 m-0 text-center">
        <div class="proceedToFile">
            <div class="text">
                <p class="bg-warning p-2 rounded"><?= $authObj["message"] ?>!</p>
                <p class="text-sm text-warning" id="complianceAuthOtpVerifyResponse"></p>
                <button class="btn btn-primary btn-sm" id="complianceAuthSendOtpBtn">Send Otp</button>
            </div>
        </div>
    </div>
<?php
}

?>
<script>
    $(document).ready(function() {
        $(document).on("input", "#OtpInputs", function(e) {
            var target = $(e.target);
            var val = target.val();
            if (isNaN(val)) {
                target.val("");
                return;
            }
            if (val !== "") {
                var next = target.next();
                if (next.length > 0) {
                    next.focus();
                }
            }
        });

        $(document).on("keyup", "#OtpInputs", function(e) {
            var target = $(e.target);
            var key = e.key.toLowerCase();
            if (key === "backspace" || key === "delete") {
                target.val("");
                var prev = target.prev();
                if (prev.length > 0) {
                    prev.focus();
                }
                return;
            }
        });


        $(document).on("click", "#complianceAuthSendOtpBtn", function() {
            console.log("complianceAuthSendOtpBtn");
            $.ajax({
                method: "post",
                url: "<?= BASE_URL ?>branch/gstr/ajax/ajax-auth-gst-portal.php",
                data: {
                    act: 'sendOtp'
                },
                beforeSend: function() {
                    $("#complianceAuthSendOtpBtn").html("Sending...");
                    $("#complianceAuthSendOtpBtn").prop("disabled", true);
                    $("#complianceAuthOtpVerifyResponse").html("");
                },
                success: function(data) {
                    let response = JSON.parse(data);
                    if (response["status"] != "success") {
                        $("#complianceAuthSendOtpBtn").html("Send OTP");
                        $("#complianceAuthSendOtpBtn").prop("disabled", false);
                        $("#complianceAuthOtpVerifyResponse").html(`${response["message"]}!`);
                    } else {
                        $("#complianceAuthDiv").html(`<div class="proceedToFile">
                                    <div class="text">
                                        <p class="text-sm">OTP has been sent to the registered mobile number.</p>
                                        <p class="text-sm">Please enter the otp and processed</p>
                                        <div id="OtpInputs" class="otp-inputs d-flex gap-2 my-3">
                                            <input class="input form-control authVerifyOtpInput" type="text" inputmode="numeric" maxlength="1" />
                                            <input class="input form-control authVerifyOtpInput" type="text" inputmode="numeric" maxlength="1" />
                                            <input class="input form-control authVerifyOtpInput" type="text" inputmode="numeric" maxlength="1" />
                                            <input class="input form-control authVerifyOtpInput" type="text" inputmode="numeric" maxlength="1" />
                                            <input class="input form-control authVerifyOtpInput" type="text" inputmode="numeric" maxlength="1" />
                                            <input class="input form-control authVerifyOtpInput" type="text" inputmode="numeric" maxlength="1" />
                                        </div>
                                        <p class="text-sm text-warning" id="complianceAuthOtpVerifyResponse"></p>
                                        <button class="btn btn-primary" id="complianceAuthOtpVerifyBtn">Verify OTP</button>
                                    </div>
                                </div>`);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#complianceAuthSendOtpBtn").html("Send OTP");
                    $("#complianceAuthSendOtpBtn").prop("disabled", false);
                }
            });
        });

        $(document).on("click", "#complianceAuthOtpVerifyBtn", function() {
            let formOtp = 0;
            $(".authVerifyOtpInput").each(function() {
                formOtp = (formOtp * 10) + parseInt($(this).val());
            });
            console.log("complianceAuthOtpVerifyBtn");
            let authOtp = formOtp;
            if (formOtp > 100000) {
                $.ajax({
                    method: "post",
                    url: "<?= BASE_URL ?>branch/gstr/ajax/ajax-auth-gst-portal.php",
                    data: {
                        act: 'verifyOtp',
                        authOtp: authOtp
                    },
                    beforeSend: function() {
                        $("#complianceAuthOtpVerifyBtn").html("Loding...");
                        $("#complianceAuthOtpVerifyBtn").prop("disabled", true);
                    },
                    success: function(data) {
                        // $("#complianceAuthOtpVerifyBtn").html("Verify OTP");
                        // $("#complianceAuthOtpVerifyBtn").prop("disabled", false);
                        console.log(data);
                        let response = JSON.parse(data);
                        $("#complianceAuthOtpVerifyResponse").html(response["message"]);
                        if (response["status"] == "success") {
                            window.location.reload();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $("#complianceAuthOtpVerifyBtn").html("Verify OTP");
                        $("#complianceAuthOtpVerifyBtn").prop("disabled", false);
                    }
                });
            } else {
                $("#complianceAuthOtpVerifyResponse").html("Please provide a valid OTP!");
                console.log("Provide a valid OTP!");
            }
        });
    });
</script>