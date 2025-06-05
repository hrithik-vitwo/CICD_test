<style>
    .content-area {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 7px;
    }
</style>

<div class="container">
    <h4 class="text-sm font-bold my-4">Generate EVC</h4>
    <div class="col-12 pl-auto">
        <div class="content-area" id="content-area">
            <div class="text mb-3">
                <img src="<?= BASE_URL ?>public/assets/img/VitNew 1.png" alt="">
                <div>
                    <label class="text-sm">PAN </label>
                    <input type="text" placeholder="Enter Pan.." class="form-control" id="panInputBox">
                    <small>(Enter your registered PAN, which is set in the GST portal)</small>
                    <div id="evc-content">
                        <button class="btn btn-primary border mt-2" id="generateEvcButton">Generate EVC</button><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        $("#fileGstr1StageConnect").addClass("progress-success");
        $("#fileGstr1StageSaveFile").addClass("progress-success");
        $("#fileGstr1StageProceedFile").addClass("progress-success");
        // $("#fileGstr1StageGenerateEvc").addClass("progress-success");
        // $("#fileGstr1StageFile").addClass("progress-success");

        $(document).on("click", "#generateEvcButton", function() {
            // ajax for generate evc
            let pan = $('#panInputBox').val();
            if (pan != "" && pan.length==10) {
                $.ajax({
                    type: "POST",
                    url: "<?= BASE_URL ?>branch/compliance/ajaxs/api/ajax-gstr1-generate-evc.php?action=<?= base64_encode(json_encode($queryParams)) ?>",
                    beforeSend: function() {
                        console.log("Saving Data");
                        $('#generateEvcButton').html('Generating...');
                    },
                    data: {
                        pan
                    },
                    success: function(response) {
                        console.log(response);
                        Swal.fire({
                            icon: `${response.status}`,
                            title: `${response.status[0].toUpperCase()+response.status.substr(1)}!`,
                            text: `${response.message.split(" or ")[0]}!`,
                        });
                        if (response.status == "success") {
                            $("#panInputBox").attr("readonly", true);
                            $("#evc-content").html(`
                                <label class="text-sm">OTP </label>
                                <input type="text" class="form-control" placeholder="Enter OTP..." id="otpInputBox">
                                <small>(Check your registered phone number, which is set in the GST portal)</small>
                                <button class="btn btn-primary border mt-2" id="verifyEvcAndFile">Verify & File</button>
                            `);
                            $("#fileGstr1StageGenerateEvc").addClass("progress-success");
                        }
                    },
                    complete: function() {
                        console.log("Data saved successfully")
                        $('#generateEvcButton').html('Generate EVC');
                    }
                });
            } else {
                Swal.fire({
                    icon: `warning`,
                    title: `Warning!`,
                    text: `Please provide valid PAN details!`,
                });
            }
        });

        // verify and file

        $(document).on("click", "#verifyEvcAndFile", function() {
            let pan = $('#panInputBox').val();
            let otp = $('#otpInputBox').val();
            if (otp != "") {
                $.ajax({
                    type: "POST",
                    url: "<?= BASE_URL ?>branch/compliance/ajaxs/api/ajax-gstr1-verify-evc-and-file.php?action=<?= base64_encode(json_encode($queryParams)) ?>",
                    data: {
                        pan: pan,
                        otp: otp
                    },
                    beforeSend: function() {
                        console.log("Saving Data");
                        $('#verifyEvcAndFile').html('Filling...');
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status == "success") {
                            Swal.fire({
                                icon: `${response.status}`,
                                title: `${response.status[0].toUpperCase()+response.status.substr(1)}!`,
                                text: `${response.message.split(" or ")[0]}!`,
                            }).then(function() {
                                window.location.href = `<?= BASE_URL ?>branch/compliance/gstr1-concised-view.php`;
                            })
                            $("#fileGstr1StageFile").addClass("progress-success");
                        } else {
                            Swal.fire({
                                icon: `${response.status}`,
                                title: `${response.status[0].toUpperCase()+response.status.substr(1)}!`,
                                text: `${response.message.split(" or ")[0]}!`,
                            });
                        }
                    },
                    complete: function() {
                        console.log("Data saved successfully")
                        $('#verifyEvcAndFile').html('Verify & File');
                    }
                });
            } else {
                Swal.fire({
                    icon: `warning`,
                    title: `Warning!`,
                    text: `Please enter the OTP!`,
                });
            }
        });
    })
</script>