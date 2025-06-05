<style>
    .content-area {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 7px;
    }

    div#OtpInputs input {
        text-align: center;
        font-weight: 600;
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
                                <input class="input form-control py-3" type="text" inputmode="numeric" maxlength="1" style="width: 40px;">
                                <input class="input form-control py-3" type="text" inputmode="numeric" maxlength="1" style="width: 40px;">
                                <input class="input form-control py-3" type="text" inputmode="numeric" maxlength="1" style="width: 40px;">
                                <input class="input form-control py-3" type="text" inputmode="numeric" maxlength="1" style="width: 40px;">
                                <input class="input form-control py-3" type="text" inputmode="numeric" maxlength="1" style="width: 40px;">
                                <input class="input form-control py-3" type="text" inputmode="numeric" maxlength="1" style="width: 40px;">
                            </div>
                            <a class="btn btn-primary waves-effect waves-light" id="verifyOtpButton">Verify Otp</a>
                        `);
                        const inputs = document.querySelectorAll('#OtpInputs .input');
                        inputs.forEach((input, index) => {
                            input.addEventListener('input', (e) => {
                                const currentInput = e.target;
                                if (currentInput.value.length === 1 && index < inputs.length - 1) {
                                    inputs[index + 1].focus(); // Focus the next input
                                }
                            });

                            input.addEventListener('keydown', (e) => {
                                if (e.key === 'Backspace' && index > 0 && !input.value) {
                                    inputs[index - 1].focus(); // Go back to the previous input on backspace
                                }
                            });
                        });
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
            let otp = "";
            document.querySelectorAll('#OtpInputs .input').forEach(input => {
                otp += input.value.toString();
            });

            if (otp == "" || otp.length < 6) {
                Swal.fire({
                    icon: `warning`,
                    title: `Warning!`,
                    text: `Please provide OTP!`,
                });
            }

            console.log("OTP:", otp);

            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>branch/compliance/ajaxs/api/ajax-gst-portal-auth.php?action=verifyOtp",
                data: {
                    otp: otp
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

                    if (response.status == "success") {
                        location.reload();
                    }
                },
                complete: function() {
                    $("#verifyOtpButton").html("Verify Otp Again");
                    console.log("Ajax call completed!");
                }
            });
        });
    });
</script>