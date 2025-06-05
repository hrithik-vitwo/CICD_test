<?php

// require_once("../../app/v1/connection-branch-admin.php");
// require_once("../../app/v1/functions/branch/func-compliance-controller.php");

$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
// $authGstinPortalObj->refreshToken()
if ($authObj["status"] == "success") {
?>
    <div id="complianceAuthDiv" class="row p-0 m-0 text-center">
        <div class="col-lg-12 p-2 bg-warning">
            <p class="text-success text-center"><?= $authObj["message"]; ?></p>
        </div>
    </div>
<?php
} else {
?>
    <div id="complianceAuthDiv" class="row p-0 m-0 text-center">
        <div class="col-lg-12 p-2 bg-warning">
            <span><?= $authObj["message"] ?>!</span>
            <button class="btn btn-primary btn-sm" id="complianceAuthSendOtpBtn">Send Otp</button>
        </div>
    </div>
<?php
}

?>

<script>
    $(document).ready(function() {
        $(document).on("click", "#complianceAuthSendOtpBtn", function() {
            console.log("complianceAuthSendOtpBtn");
            $.ajax({
                method: "post",
                url: "<?= BASE_URL ?>branch/gstr/ajax/ajax-auth-gst-portal.php",
                data: {
                    act: 'sendOtp'
                },
                beforeSend: function() {
                    $("#complianceAuthSendOtpBtn").html("Loding...");
                    $("#complianceAuthSendOtpBtn").prop("disabled", true);
                },
                success: function(data) {
                    // $("#complianceAuthSendOtpBtn").html("Send OTP");
                    // $("#complianceAuthSendOtpBtn").prop("disabled", false);
                    // console.log(data);
                    let response = JSON.parse(data);
                    if(response["status"]=="success"){
                        $("#complianceAuthDiv").html(`
                            <div class="col-lg-12 p-2 bg-warning">
                                <div class="d-flex text-center justify-content-center gap-1">
                                    <input class="form-control w-25 col-lg-1" id="complianceAuthOtpInput" type="number" max="999999" placeholder="e.g 999999">
                                    <button class="btn btn-primary" id="complianceAuthOtpVerifyBtn">Verify Otp</button>
                                </div>
                                <span class="text-danger text-xs" id="complianceAuthOtpVerifyResponse"></span>
                            </div>
                        `);
                    }
                    
                },
                error: function (jqXHR, textStatus, errorThrown){
                    $("#complianceAuthSendOtpBtn").html("Send OTP");
                    $("#complianceAuthSendOtpBtn").prop("disabled", false);
                }
            });
        });

        $(document).on("click", "#complianceAuthOtpVerifyBtn", function(){
            console.log("complianceAuthOtpVerifyBtn");
            let authOtp = $("#complianceAuthOtpInput").val();
            if(authOtp!=""){
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
                        if(response["status"]=="success"){
                            window.location.reload();
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown){
                        $("#complianceAuthOtpVerifyBtn").html("Verify OTP");
                        $("#complianceAuthOtpVerifyBtn").prop("disabled", false);
                    }
                });
            }else{
                $("#complianceAuthOtpVerifyResponse").html("Please provide a valid OTP!");
                console.log("Provide a valid OTP!");
            }
        });
    });
</script>