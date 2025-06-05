<style>
    .content-area {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 7px;
    }
</style>

<div class="container">
    <h4 class="text-sm font-bold my-4">Proceed File</h4>
    <div class="col-12 pl-auto">
        <div class="content-area" id="content-area">
            <?php
            // $gstr1JsonRepoObj = new Gstr1JsonRepository($queryParams->period, date("Y-m-d", strtotime($queryParams->startDate)), date("Y-m-d", strtotime($queryParams->endDate)));
            // $jsonObj = $gstr1JsonRepoObj->generate();
            // console($jsonObj["hsn"]["data"]);
            ?>
            <div class="text">
                <img src="<?= BASE_URL ?>public/assets/img/VitNew 1.png" alt="">
                <p class="text-sm font-bold">Would you like to procced with saved data?</p>
                <p class="text-sm font-bold">If you want to reset and procced please click on reset!</p>
                <div class="btns-group my-3">
                    <button class="btn btn-primary reset-btn" id="gstr1ResetDataButton">Reset the Data</button>
                    <button class="btn btn-primary border mr-2" id="gstr1ProccedDataButton">Procced to file</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#fileGstr1StageConnect").addClass("progress-success");
        $("#fileGstr1StageSaveFile").addClass("progress-success");
        // $("#fileGstr1StageProceedFile").addClass("progress-success");
        // $("#fileGstr1StageGenerateEvc").addClass("progress-success");
        // $("#fileGstr1StageFile").addClass("progress-success");

        $(document).on("click", "#gstr1ResetDataButton", function() {
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>branch/compliance/ajaxs/api/ajax-gstr1-reset.php?action=<?= base64_encode(json_encode($queryParams)) ?>",
                beforeSend: function() {

                    console.log("Saving Data");
                    $('#gstr1ResetDataButton').html('Reseting...');
                },
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: `${response.status}`,
                        title: `${response.status[0].toUpperCase()+response.status.substr(1)}!`,
                        text: `${response.message.split(" or ")[0]}!`,
                    });
                    getGstr1CurrentStage();
                },
                complete: function() {
                    console.log("Data saved successfully")
                    $('#gstr1ResetDataButton').html('Reset the Data');
                }
            })
        });

        $(document).on("click", "#gstr1ProccedDataButton", function() {
            $.ajax({
                type: "POST",
                url: "<?= BASE_URL ?>branch/compliance/ajaxs/api/ajax-gstr1-proceed.php?action=<?= base64_encode(json_encode($queryParams)) ?>",
                beforeSend: function() {
                    console.log("Saving Data");
                    $('#gstr1ProccedDataButton').html('Processing...');
                },
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: `${response.status}`,
                        title: `${response.status[0].toUpperCase()+response.status.substr(1)}!`,
                        text: `${response.message.split(" or ")[0]}!`,
                    });
                    getGstr1CurrentStage();
                },
                complete: function() {
                    console.log("Data saved successfully")
                    $('#gstr1ProccedDataButton').html('Procced to file');
                }
            })
        });
    });
</script>