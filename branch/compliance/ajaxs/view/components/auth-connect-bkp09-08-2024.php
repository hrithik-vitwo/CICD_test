<style>
    .content-area {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 7px;
    }
</style>

<div class="container">
    <h4 class="text-sm font-bold my-4">Connect Portal</h4>
    <div class="col-12 pl-auto">
        <div class="content-area" id="content-area">
            <p class="text-sm">Please click below button to send OTP to connect with portal!</p>
            <a class="btn btn-primary waves-effect waves-light" id="sendOtpButton">Send OTP</a>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        console.log("I am ready!");
        $(document).on("click", "#sendOtpButton", function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>branch/compliance/ajaxs/api/ajax-gst-portal-auth.php?action=sendOtp",
                beforeSend: function() {
                    $("#sendOtpButton").html("Sending...");
                    console.log("Api call started!");
                },
                success: function(response) {
                    console.log(response);
                    if (response.status == "success") {
                        $("#content-area").html(`
                            <p class="text-sm">OTP has been sent to the registered mobile number.</p>
                            <p class="text-sm">Please enter the otp and processed</p>
                            <div id="OtpInputs" class="otp-inputs d-flex gap-2 my-3">
                                <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
                                <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
                                <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
                                <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
                                <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
                                <input class="input form-control" type="text" inputmode="numeric" maxlength="1">
                            </div>
                            <a class="btn btn-primary waves-effect waves-light" id="verifyOtpButton">Verify Otp</a>
                        `);
                    } else {
                        Swal.fire({
                            icon: `${response.status}`,
                            title: `${response.status[0].toUpperCase()+response.status.substr(1)}!`,
                            text: `${response.message.split(" or ")[0]}!`,
                        });
                    }
                },
                complete: function(xhr, status) {
                    $("#sendOtpButton").html("Send Otp Again");
                    console.log("Ajax call completed!");
                    console.log(xhr, status);
                }
            });
        });

        $(document).on("click", "#verifyOtpButton", function(e) {
            e.preventDefault();
            // let otp = 6789;
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>branch/compliance/ajaxs/api/ajax-gst-portal-auth.php?action=verifyOtp",
                data: {
                    otp
                },
                beforeSend: function() {
                    $("#verifyOtpButton").html("Verifying...");
                    console.log("Api call started!");
                },
                success: function(response) {
                    console.log("RESPONSE:");
                    console.log(response);
                    Swal.fire({
                        icon: `${response.status}`,
                        title: `${response.status}`,
                        text: `${response.message}`,
                    });
                    
                },
                complete: function() {
                    $("#verifyOtpButton").html("Verify Otp Again");
                    console.log("Ajax call completed!");
                }
            });
        });
    });
</script>